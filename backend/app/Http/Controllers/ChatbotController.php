<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Send a message to Gemini API with SerVora context
     */
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');

        if (!$message) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        // SerVora platform context for the chatbot
        $servoraContext = $this->getServoraContext();
        
        // Detect user intent and provide context-aware response
        $contextualPrompt = $this->buildContextualPrompt($message, $servoraContext);

        // ✅ Use a valid Gemini model (replace with one from /gemini-models)
        $modelName = "models/gemini-2.5-pro";
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/{$modelName}:generateContent?key=" . env('GEMINI_API_KEY');

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            // ⚡ Disable SSL verification temporarily if you have certificate issues
            ->withoutVerifying()
            ->post($endpoint, [
                'contents' => [
                    ['parts' => [['text' => $contextualPrompt]]]
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Gemini API request failed',
                    'status' => $response->status(),
                    'body' => $response->json()
                ], 500);
            }

            return response()->json($response->json());

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('Gemini RequestException: ' . $e->getMessage());
            return response()->json([
                'error' => 'RequestException',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('General Exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Exception',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optional: List available Gemini models
     */
    public function listModels()
    {
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models?key=" . env('GEMINI_API_KEY');

        try {
            $response = Http::get($endpoint);

            if ($response->failed()) {
                Log::error('Failed to fetch Gemini models', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'error' => 'Failed to retrieve models list',
                    'details' => $response->body()
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Exception fetching models: ' . $e->getMessage());
            return response()->json([
                'error' => 'Exception',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive SerVora platform context for chatbot training
     */
    private function getServoraContext()
    {
        return "You are AppointMe Assistant, an AI helper for the AppointMe appointment management platform. Here's what you need to know about AppointMe:

ABOUT APPOINTME:
AppointMe is a trusted marketplace for home services in Bangladesh. It connects customers with verified service providers for various home services. The platform offers transparent pricing, trusted support, and quality service delivery.

PLATFORM FEATURES:
1. SERVICE BOOKING SYSTEM
   - Customers can browse and book various home services
   - Services include: Home Cleaning, AC Servicing, Electrical Works, Plumbing, Beauty & Grooming, Appliance Repair
   - Automatic booking with user ID, payment status, and booking time
   - Real-time service availability checking
   - Payment integration for secure transactions

2. USER MANAGEMENT
   - Customer registration and login
   - Provider registration and verification process
   - Admin dashboard for platform management
   - Profile management for all user types
   - Booking history tracking

3. PROVIDER APPLICATION SYSTEM
   - Service providers can apply to join the platform
   - Admin approval process for provider applications
   - Verification system for quality assurance
   - Provider profile management
   - Service listing and management

4. ADMIN FEATURES
   - Provider application review and approval
   - User and service management
   - Platform analytics and reporting
   - Quality control and monitoring
   - Customer support management

HOW TO USE APPOINTME:
1. CUSTOMERS:
   - Sign up/Login to your account
   - Browse available services
   - Select a service and provider
   - Book appointment with automatic details
   - Make payment through secure gateway
   - Track booking status
   - Rate and review services

2. SERVICE PROVIDERS:
   - Apply to become a provider
   - Wait for admin approval
   - Set up your service profile
   - Manage your availability
   - Receive booking notifications
   - Complete services and get paid

3. ADMINS:
   - Review provider applications
   - Approve/reject applications
   - Monitor platform activities
   - Handle customer support
   - Manage platform settings

BOOKING PROCESS:
1. Customer selects a service
2. System auto-populates user ID and booking time
3. Payment status is automatically managed
4. Provider receives booking notification
5. Service is completed
6. Customer can rate and review

PROVIDER APPROVAL PROCESS:
1. Provider submits application
2. Admin reviews application details
3. Verification of credentials
4. Admin approves or rejects
5. Approved providers can start offering services

PAYMENT SYSTEM:
- Secure payment gateway integration
- Automatic payment status tracking
- Transparent pricing
- Multiple payment options

QUALITY ASSURANCE:
- Verified providers only
- Customer rating and review system
- Quality monitoring by admins
- Customer support available

SUPPORT FEATURES:
- Help Center with FAQs
- Customer support chat
- Order tracking
- Refund policy
- Terms of service and privacy policy

PLATFORM COVERAGE:
- Citywide coverage in Dhaka, Bangladesh
- 200+ different services available
- Verified and trusted providers
- 24/7 customer support

Please help users with questions about booking services, understanding the platform, provider applications, admin processes, payment issues, or any other AppointMe-related topics. Always be helpful, professional, and provide accurate information about the platform.";
    }

    /**
     * Build contextual prompt based on user intent
     */
    private function buildContextualPrompt($userMessage, $context)
    {
        $intent = $this->detectUserIntent($userMessage);
        
        $contextualInfo = "";
        switch ($intent) {
            case 'booking':
                $contextualInfo = "\n\nSPECIAL FOCUS: The user is asking about booking services. Provide detailed information about the booking process, available services, pricing, and how the automatic system works.";
                break;
            case 'provider':
                $contextualInfo = "\n\nSPECIAL FOCUS: The user is asking about becoming a provider or provider-related topics. Focus on the application process, requirements, approval process, and how providers can manage their services.";
                break;
            case 'admin':
                $contextualInfo = "\n\nSPECIAL FOCUS: The user is asking about admin functions. Explain how admins review applications, manage the platform, and handle approvals/rejections.";
                break;
            case 'payment':
                $contextualInfo = "\n\nSPECIAL FOCUS: The user is asking about payments. Provide information about the payment system, security, automatic status tracking, and transaction process.";
                break;
            case 'technical':
                $contextualInfo = "\n\nSPECIAL FOCUS: The user has a technical question. Provide step-by-step guidance and troubleshooting information.";
                break;
            default:
                $contextualInfo = "\n\nProvide a comprehensive and helpful response about AppointMe.";
        }

        return $context . $contextualInfo . "\n\nUser Question: " . $userMessage . "\n\nAppointMe Assistant Response:";
    }

    /**
     * Detect user intent from message
     */
    private function detectUserIntent($message)
    {
        $message = strtolower($message);
        
        // Booking related keywords
        if (preg_match('/\b(book|booking|appointment|service|schedule|available|price|cost)\b/', $message)) {
            return 'booking';
        }
        
        // Provider related keywords
        if (preg_match('/\b(provider|become|apply|application|join|register|verification|approve)\b/', $message)) {
            return 'provider';
        }
        
        // Admin related keywords
        if (preg_match('/\b(admin|approve|reject|manage|dashboard|review|application)\b/', $message)) {
            return 'admin';
        }
        
        // Payment related keywords
        if (preg_match('/\b(payment|pay|money|transaction|refund|billing|charge)\b/', $message)) {
            return 'payment';
        }
        
        // Technical/help keywords
        if (preg_match('/\b(how|help|error|problem|issue|support|trouble|login|password)\b/', $message)) {
            return 'technical';
        }
        
        return 'general';
    }

    /**
     * Get quick response suggestions for users
     */
    public function getQuickResponses()
    {
        return response()->json([
            'quick_responses' => [
                "How do I book a service?",
                "How to become a provider?",
                "What services are available?",
                "How does the payment system work?",
                "How do admins approve providers?",
                "What are the service charges?",
                "How to track my booking?",
                "How to contact support?",
                "What areas do you cover?",
                "How to cancel a booking?"
            ]
        ]);
    }

    /**
     * Get popular FAQs
     */
    public function getFAQs()
    {
        return response()->json([
            'faqs' => [
                [
                    'question' => 'How does AppointMe booking work?',
                    'answer' => 'Simply browse our services, select a provider, choose your preferred time, and book. Your user ID and booking time are automatically filled. Payment is processed securely through our gateway.'
                ],
                [
                    'question' => 'How to become a AppointMe provider?',
                    'answer' => 'Apply through our provider application form. Our admin team will review your application, verify your credentials, and approve qualified providers. Once approved, you can start offering services.'
                ],
                [
                    'question' => 'What services does AppointMe offer?',
                    'answer' => 'We offer 200+ home services including Home Cleaning, AC Servicing, Electrical Works, Plumbing, Beauty & Grooming, and Appliance Repair across Dhaka, Bangladesh.'
                ],
                [
                    'question' => 'How secure are payments on AppointMe?',
                    'answer' => 'We use secure payment gateways with automatic status tracking. All transactions are encrypted and protected. Multiple payment options are available for your convenience.'
                ],
                [
                    'question' => 'How do I track my booking?',
                    'answer' => 'Log into your account and visit the Booking History page from your profile menu. You can see all your bookings with real-time status updates.'
                ]
            ]
        ]);
    }
}
