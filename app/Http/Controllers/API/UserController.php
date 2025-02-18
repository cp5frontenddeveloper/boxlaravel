<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        return User::with(['subusers', 'devices', 'codeLists'])->get();
        // $users = User::all();
        // return response()->json($users);
    }

        /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|min:8|unique:users,phone_number',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Generate a random code for email verification
        $tempCode = rand(100000, 999999); // Generate a random 6-character string

        // Create the user and store the tempCode
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'tempCode' => $tempCode,
        ]);

        // Send the code to the user's email
        Mail::raw("Your verification code is: {$tempCode}", function ($message) use ($user) {
            $message->to($user->email)  // تأكد أن هذا البريد الإلكتروني معتمد في Mailgun
                    ->subject('Email Verification Code');
        });




        return response()->json(['message' => 'User registered successfully. Please check your email for the verification code.', 'data' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with(['subusers', 'devices', 'codeLists'])->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'sometimes|nullable|string|min:8|unique:users,phone_number,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'phone_number' => $request->phone_number ?? $user->phone_number,
        ]);

        return response()->json($user);
    }


    // Rest password
    public function sendResetCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Generate a new 6-digit numeric verification code
        $newTempCode = rand(100000, 999999);

        // Update the user's tempCode with the new code
        $user->tempCode = $newTempCode;
        $user->save();

        // Log the new tempCode (for testing purposes)
        Log::info('New tempCode for ' . $user->email . ': ' . $newTempCode);

        // Send the tempCode via email
        Mail::raw("Your password reset code is: $newTempCode", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset Code');
        });

        return response()->json([
            'state' => 'success',
        ], 200);
    }

    // verify code to change the password
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'tempCode' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the provided tempCode matches the one in the database
        if ($user->tempCode == $request->tempCode) {
            return response()->json([
                'state' => 'success',
            ], 200);
        } else {
            return response()->json([
                'message' => 'The reset code is incorrect.',
            ], 400);
        }
    }

    // Verification Email
    public function verifyTempCode(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'tempCode' => 'required|string|max:6',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // If the code does not match
        if ($user->tempCode !== $request->tempCode) {
            return response()->json(['error' => 'Invalid verification code'], 400);
        }

        // If the code matches, clear the tempCode and mark the email as verified
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['state' => 'success'], 200);
    }

    // Store new password
    public function storeNewPassword(Request $request)
{
    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'password' => 'required|string|min:8|confirmed', // Ensure password_confirmation is included in the request
    ]);

    // If validation fails, return errors
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Find the user by email
    $user = User::where('email', $request->email)->first();

    // Check if the user exists
    if (!$user) {
        return response()->json(['state' => 'failed', 'message' => 'User not found'], 404);
    }
    // Update the user's password and save
    $user->password = Hash::make($request->password);
    $user->save();

    // Return success response
    return response()->json(['state' => 'successfull'], 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
    public function getusersCountByUserId($userId)
{
    // ابحث عن جميع المستخدمين المرتبطين بالمستخدم بواسطة user_id
    $userCount = User::where('id', $userId)->count();

    if ($userCount == 0) {
        return response()->json(['error' => 'No users found for this ID'], 404);
    }

    return response()->json(['users_count' => $userCount], 200);
}

/**
 * Send OTP to phone number
 */
public function sendOTP(Request $request)
{
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required|string|exists:users,phone_number',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Find user by phone number
    $user = User::where('phone_number', $request->phone_number)->first();

    // Generate new 6-digit OTP
    $otp = rand(100000, 999999);

    // Store OTP in tempCode field
    $user->tempCode = $otp;
    $user->save();

    // Log the OTP (in production, you would send this via SMS)
    Log::info('OTP for ' . $user->phone_number . ': ' . $otp);

    return response()->json([
        'message' => 'OTP sent successfully',
        'state' => 'success'
    ]);
}

/**
 * Verify phone OTP
 */
public function verifyPhoneOTP(Request $request)
{
    $validator = Validator::make($request->all(), [
        'phone_number' => 'required|string|exists:users,phone_number',
        'otp' => 'required|string|max:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Find user by phone number
    $user = User::where('phone_number', $request->phone_number)->first();

    // Verify OTP
    if ($user->tempCode !== $request->otp) {
        return response()->json([
            'message' => 'Invalid OTP',
            'state' => 'error'
        ], 400);
    }

    // Clear the OTP after successful verification
    $user->tempCode = null;
    $user->save();

    return response()->json([
        'message' => 'OTP verified successfully',
        'state' => 'success',
        'data' => $user
    ]);
}

public function logout(Request $request)
{
    // Validate token in request
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Find user and revoke all tokens
    $user = User::find($request->user_id);
    if ($user) {
        $user->tokens()->delete(); // This will delete all tokens for the user
        return response()->json([
            'message' => 'Successfully logged out',
            'state' => 'success'
        ]);
    }

    return response()->json([
        'message' => 'User not found',
        'state' => 'error'
    ], 404);
}

public function deleteAccount(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'password' => 'required|string'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $user = User::find($request->user_id);

    if (!$user) {
        return response()->json([
            'message' => 'User not found',
            'state' => 'error'
        ], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid password',
            'state' => 'error'
        ], 400);
    }

    // حذف جميع العلاقات
    $user->devices()->delete();
    $user->subusers()->delete();
    $user->codeLists()->delete();
    $user->tokens()->delete();
    
    $user->delete();

    return response()->json([
        'message' => 'Account successfully deleted',
        'state' => 'success'
    ]);
}

}
