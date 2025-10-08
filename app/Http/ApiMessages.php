<?php

namespace App\Http;

class ApiMessages
{
    public const SERVER_ERROR = "Server error";
    public const VALIDATION_FAILED = "Validation failed";
    public const ROUTE_NOT_FOUND = "Route not found";
    public const METHOD_NOT_ALLOWED = "Method not allowed";
    public const CSRF_TOKEN_MISMATCH = "CSRF token mismatch";
    public const INVALID_SIGNATURE = "Invalid or expired verification link";

    public const AUTHORIZATION_EXCEPTION = "Authorization exception";
    public const AUTHENTICATION_EXCEPTION = "Authentication exception";
    public const FORBIDDEN = "Not enough rights";

    public const MODEL_NOT_FOUND = "Record not found";

    public const USER_REGISTERED = "User registered. Please verify your email";
    public const LOGOUT = "Logged out";

    public const EMAIL_VERIFIED = "Email verified successfully";
    public const EMAIL_NOT_VERIFIED = "Email not verified";
    public const EMAIL_RESENDED = "Please verify your email";

    public const ENTITY_UPDATED = "Entity updated";
    public const ENTITY_DELETED = "Entity deleted";

}
