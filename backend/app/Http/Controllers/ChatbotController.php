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
        $message = $request->input('message', '');

        if (empty($message)) {
            return response()->json([
                'success' => false, 
                'error' => 'Message is required'
            ], 400);
        }

        // Log incoming message
        Log::info('Chatbot message received', ['message' => $message]);

        // Check if API key is set
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            // Use fallback response when API key is not set
            $fallbackResponse = $this->getFallbackResponse($message);
            Log::info('Using fallback - no API key');
            return response()->json([
                'success' => true,
                'message' => $fallbackResponse,
                'type' => 'fallback_no_api_key'
            ]);
        }

        // SerVora platform context for the chatbot
        $servoraContext = $this->getServoraContext();
        
        // Detect user intent and provide context-aware response
        $contextualPrompt = $this->buildContextualPrompt($message, $servoraContext);

        // ✅ Use a valid Gemini model - Updated to correct model name
        $modelName = "models/gemini-1.5-flash";
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/{$modelName}:generateContent?key=" . $apiKey;

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                // ⚡ Disable SSL verification temporarily if you have certificate issues
                ->withoutVerifying()
                ->post($endpoint, [
                    'contents' => [
                        ['parts' => [['text' => $contextualPrompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ]
                ]);

            if ($response->failed()) {
                Log::error('Gemini API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $endpoint
                ]);

                // Use fallback response when API fails
                $fallbackResponse = $this->getFallbackResponse($message);
                return response()->json([
                    'success' => true,
                    'message' => $fallbackResponse,
                    'type' => 'fallback_api_failed'
                ]);
            }

            $responseData = $response->json();
            
            // Extract the text response from Gemini's response structure
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                return response()->json([
                    'success' => true,
                    'message' => $responseData['candidates'][0]['content']['parts'][0]['text']
                ]);
            }

            return response()->json($responseData);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('Gemini RequestException: ' . $e->getMessage());
            // Use fallback response on network error
            $fallbackResponse = $this->getFallbackResponse($message);
            return response()->json([
                'success' => true,
                'message' => $fallbackResponse,
                'type' => 'fallback_network_error'
            ]);
        } catch (\Exception $e) {
            Log::error('General Exception in ChatbotController: ' . $e->getMessage());
            // Use fallback response on any error
            $fallbackResponse = $this->getFallbackResponse($message);
            return response()->json([
                'success' => true,
                'message' => $fallbackResponse,
                'type' => 'fallback_general_error'
            ]);
        }
    }

    /**
     * Fallback method when Gemini API is not available
     */
    public function getFallbackResponse($message)
    {
        $originalMessage = $message; // Keep original for logging
        $message = strtolower(trim($message)); // Clean the message
        
        // Log the message for debugging
        Log::info('Chatbot fallback triggered', [
            'original_message' => $originalMessage,
            'processed_message' => $message
        ]);
        
        // Empty message check
        if (empty($message)) {
            return "🤖 **Hello! I'm AppointMe Assistant.**\n\nI'm here to help you with anything related to our home services platform. You can ask me about booking services, becoming a provider, payments, or any other questions!\n\nWhat can I help you with today?";
        }
        
        // Greeting responses
        if (preg_match('/\b(hello|hi|hey|good morning|good afternoon|good evening|greetings)\b/', $message)) {
            return "Hello! Welcome to AppointMe, your trusted marketplace for home services in Bangladesh. How can I assist you today? You can ask me about booking services, becoming a provider, payments, or anything else related to our platform.";
        }
        
        // Booking-related responses
        if (preg_match('/\b(book|booking|appointment|schedule|reserve)\b/', $message)) {
            return "📅 **How to Book a Service on AppointMe:**\n\n1. Browse our 200+ available services\n2. Select a service that fits your needs\n3. Choose a verified provider\n4. Pick your preferred date and time\n5. Complete secure payment\n6. Get instant confirmation!\n\n**Available Services:** Home Cleaning, AC Servicing, Electrical Works, Plumbing, Beauty & Grooming, Appliance Repair and more. Your user ID and booking details are automatically handled by our system.";
        }
        
        // Provider application responses
        if (preg_match('/\b(provider|become|apply|application|join|register|verification)\b/', $message)) {
            return "👷 **Become a AppointMe Service Provider:**\n\n**Requirements:**\n• Valid credentials and documentation\n• Relevant skills and experience\n• Professional commitment\n\n**Process:**\n1. Submit your application with documents\n2. Admin team reviews your credentials\n3. Background verification process\n4. Get approval notification\n5. Set up your service profile\n6. Start receiving bookings!\n\n**Benefits:** Verified provider badge, steady income, flexible schedule, platform support.";
        }
        
        // Payment-related responses
        if (preg_match('/\b(payment|pay|money|transaction|refund|billing|charge|cost|price)\b/', $message)) {
            return "💳 **AppointMe Payment System:**\n\n**Security Features:**\n• Encrypted payment gateway\n• Automatic status tracking\n• Multiple payment options\n• Secure transaction processing\n\n**Payment Process:**\n1. Select your service\n2. View transparent pricing\n3. Pay securely online\n4. Get instant confirmation\n5. Service provider gets notified\n\n**Refund Policy:** Full refunds available for cancelled bookings before confirmation.";
        }
        
        // Service-related responses
        if (preg_match('/\b(service|services|available|offer|what|categories)\b/', $message)) {
            return "🏠 **AppointMe Services in Dhaka:**\n\n**Popular Categories:**\n🧹 Home Cleaning - Deep cleaning, regular maintenance\n❄️ AC Servicing - Installation, repair, maintenance\n⚡ Electrical Works - Wiring, repairs, installations\n🔧 Plumbing - Pipe repairs, installations, maintenance\n💄 Beauty & Grooming - Home salon services\n🔨 Appliance Repair - All home appliance fixes\n\n**Total:** 200+ different services available\n**Coverage:** All areas in Dhaka, Bangladesh\n**Quality:** Only verified and trusted providers";
        }
        
        // Admin-related responses
        if (preg_match('/\b(admin|approve|reject|manage|dashboard|review|control)\b/', $message)) {
            return "👨‍💼 **AppointMe Admin Functions:**\n\n**Provider Management:**\n• Review and approve applications\n• Verify provider credentials\n• Monitor service quality\n• Handle provider issues\n\n**Platform Operations:**\n• User management and support\n• Payment oversight\n• Quality assurance\n• Customer complaint resolution\n• Platform analytics and reporting\n\nAdmins ensure all providers meet our quality standards before approval.";
        }
        
        // Technical help responses
        if (preg_match('/\b(how|help|error|problem|issue|support|trouble|login|password|account)\b/', $message)) {
            return "🆘 **Need Help with AppointMe?**\n\n**Common Solutions:**\n🔐 **Login Issues:** Reset password or contact support\n👤 **Account Problems:** Check your profile settings\n📱 **App Issues:** Try refreshing or restart the app\n💳 **Payment Problems:** Verify card details or try different method\n📞 **Booking Issues:** Contact customer support\n\n**24/7 Support Available:**\n• Live chat support\n• Email assistance\n• Phone support\n• Help center with detailed guides\n\nWhat specific issue can I help you with?";
        }
        
        // Tracking responses
        if (preg_match('/\b(track|tracking|status|history|order|booking history)\b/', $message)) {
            return "📊 **Track Your AppointMe Bookings:**\n\n**How to Check Status:**\n1. Login to your account\n2. Go to 'Profile' section\n3. Click 'Booking History'\n4. View all your bookings with real-time status\n\n**Booking Statuses:**\n🟡 Pending - Waiting for provider confirmation\n🟢 Confirmed - Provider has accepted\n🔵 In Progress - Service is being performed\n✅ Completed - Service finished successfully\n❌ Cancelled - Booking was cancelled\n\nYou'll receive notifications for all status changes!";
        }
        
        // Contact/support responses
        if (preg_match('/\b(contact|support|call|phone|email|reach)\b/', $message)) {
            return "📞 **Contact AppointMe Support:**\n\n**24/7 Customer Support:**\n• 💬 Live Chat (fastest response)\n• 📧 Email Support\n• ☎️ Phone Support\n• 📱 In-app messaging\n\n**Quick Help:**\n• Check our FAQ section\n• Browse help center\n• Contact your service provider directly\n• Report issues through the app\n\n**Average Response Time:** Under 2 hours for most queries. We're here to help make your experience smooth!";
        }
        
        // Coverage area responses
        if (preg_match('/\b(area|areas|coverage|location|dhaka|bangladesh|where|available)\b/', $message)) {
            return "📍 **AppointMe Service Coverage:**\n\n**Primary Coverage:**\n🏙️ **Dhaka, Bangladesh** (Full citywide coverage)\n\n**Areas Served:**\n• Dhanmondi • Gulshan • Banani\n• Uttara • Mirpur • Wari\n• Old Dhaka • Tejgaon • Mohammadpur\n• Bashundhara • Panthapath • Farmgate\n• And all other areas in Dhaka!\n\n**Service Availability:** 7 days a week\n**Response Time:** Same-day or next-day service\n**Provider Network:** 500+ verified providers across the city";
        }
        
        // Default response for unclear queries
        if (preg_match('/\b(what|tell|about|info|information)\b/', $message)) {
            return "ℹ️ **About AppointMe Platform:**\n\nWe're Bangladesh's most trusted home services marketplace, connecting customers with verified service providers since our launch.\n\n**Why Choose AppointMe?**\n✅ 200+ different services\n✅ Verified & trusted providers\n✅ Transparent pricing\n✅ Secure payments\n✅ 24/7 customer support\n✅ Quality guarantee\n✅ Easy booking process\n\n**Ask me about:**\n• How to book services\n• Becoming a provider\n• Payment methods\n• Service areas\n• Tracking bookings\n• Platform features";
        }
        
        // Fallback for unrecognized queries
        return "🤖 **AppointMe Assistant Here!**\n\nI'm here to help you with AppointMe services! I can assist you with:\n\n📅 **Booking Services** - How to book, available services, pricing\n👷 **Becoming a Provider** - Application process, requirements\n💳 **Payments** - Payment methods, security, refunds\n📞 **Support** - Contact information, troubleshooting\n📊 **Tracking** - Booking status, history\n🏠 **Services** - Available categories, coverage areas\n\n**Just ask me something like:**\n• \"How do I book a service?\"\n• \"What services are available?\"\n• \"How to become a provider?\"\n• \"How does payment work?\"\n\nWhat would you like to know?";
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

    /**
     * Test endpoint to check if chatbot is working
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'ChatBot is working!',
            'timestamp' => now(),
            'api_key_set' => !empty(env('GEMINI_API_KEY')),
            'test_fallback' => $this->getFallbackResponse('hello')
        ]);
    }

    /**
     * Simple chatbot without external API for testing
     */
    public function simpleResponse(Request $request)
    {
        $message = $request->input('message');

        if (!$message) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        $response = $this->getFallbackResponse($message);
        
        return response()->json([
            'success' => true,
            'message' => $response,
            'type' => 'fallback_response'
        ]);
    }
}
