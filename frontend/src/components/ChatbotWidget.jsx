import { useState, useEffect, useRef } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { MessageCircle, X } from "lucide-react";
import axios from "axios";
import "../styles/Chatbot.css";
import { API_BASE } from "../api";

function ChatbotWidget() {
  const [isOpen, setIsOpen] = useState(false);
  const [messages, setMessages] = useState([
    {
      sender: "bot",
      text: "👋 Hi! I'm your AppointMe Assistant. I can help you with booking services, provider applications, payments, and more. What would you like to know?",
    },
  ]);
  const [input, setInput] = useState("");
  const [isTyping, setIsTyping] = useState(false);
  const [quickResponses, setQuickResponses] = useState([]);
  const [showQuickResponses, setShowQuickResponses] = useState(true);
  const messagesEndRef = useRef(null);

  // Load quick responses when component mounts
  useEffect(() => {
    const loadQuickResponses = async () => {
      try {
        const res = await axios.get(`${API_BASE}/chatbot/quick-responses`);
        setQuickResponses(res.data.quick_responses.slice(0, 4)); // Show first 4
      } catch (err) {
        console.log("Could not load quick responses");
      }
    };
    loadQuickResponses();
  }, []);

  // Auto-scroll when messages or typing change
  useEffect(() => {
    if (messagesEndRef.current) {
      messagesEndRef.current.scrollIntoView({ behavior: "smooth" });
    }
  }, [messages, isTyping]);

  const toggleChat = () => setIsOpen(!isOpen);

  const handleQuickResponse = (responseText) => {
    setInput(responseText);
    setShowQuickResponses(false);
  };

  const sendMessage = async () => {
    if (!input.trim()) return;

    const userMessage = { sender: "user", text: input };
    setMessages((prev) => [...prev, userMessage]);
    setShowQuickResponses(false);

    const messageToSend = input;
    setInput("");
    setIsTyping(true);

    try {
      const res = await axios.post(`${API_BASE}/chatbot`, {
        message: messageToSend,
      });

      // Handle different response formats
      let botText;
      
      if (res.data?.success && res.data?.message) {
        // New backend format with success flag
        botText = res.data.message;
      } else if (res.data?.candidates?.[0]?.content?.parts?.[0]?.text) {
        // Original Gemini API format
        botText = res.data.candidates[0].content.parts[0].text;
      } else if (res.data?.message) {
        // Simple message format
        botText = res.data.message;
      } else {
        // Fallback
        botText = "I'm here to help! Could you please rephrase your question?";
      }

      const botMessage = { sender: "bot", text: botText };

      setTimeout(() => {
        setMessages((prev) => [...prev, botMessage]);
        setIsTyping(false);
      }, 1200); // simulate typing delay
    } catch (err) {
      console.error('Chatbot error:', err);
      setMessages((prev) => [
        ...prev,
        { sender: "bot", text: "⚠️ Sorry, I'm having trouble connecting. Please try again." },
      ]);
      setIsTyping(false);
    }
  };

  return (
    <div>
      {/* Floating Button */}
      <motion.button
        onClick={toggleChat}
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.95 }}
        className="chatbot-fab"
      >
        <MessageCircle size={28} />
      </motion.button>

      {/* Chat Window */}
      <AnimatePresence>
        {isOpen && (
          <motion.div
            className="chatbot-window"
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 50 }}
            transition={{ duration: 0.3 }}
          >
            {/* Header */}
            <div className="chatbot-header">
              <div className="chatbot-logo">🤖</div>
              <div className="chatbot-info">
                <span className="chatbot-title">AppointMe Assistant</span>
                <span className="chatbot-subtitle">Here to help with your service needs</span>
              </div>
              <button onClick={toggleChat} className="chatbot-close">
                <X size={18} />
              </button>
            </div>

            {/* Messages */}
            <div className="chatbot-messages">
              {messages.map((msg, i) => (
                <div
                  key={i}
                  className={`chat-message ${
                    msg.sender === "user" ? "user" : "bot"
                  }`}
                >
                  {msg.text}
                </div>
              ))}

              {/* Quick Response Buttons */}
              {showQuickResponses && quickResponses.length > 0 && (
                <div className="quick-responses">
                  <p className="quick-responses-title">Quick questions:</p>
                  <div className="quick-responses-grid">
                    {quickResponses.map((response, i) => (
                      <button
                        key={i}
                        className="quick-response-btn"
                        onClick={() => handleQuickResponse(response)}
                      >
                        {response}
                      </button>
                    ))}
                  </div>
                </div>
              )}

              {isTyping && (
                <div className="chat-message bot typing">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              )}

              <div ref={messagesEndRef} />
            </div>

            {/* Input */}
            <div className="chatbot-input">
              <input
                type="text"
                value={input}
                onChange={(e) => setInput(e.target.value)}
                placeholder="Type your question about AppointMe..."
                onKeyDown={(e) => e.key === "Enter" && sendMessage()}
              />
              <button 
                onClick={sendMessage}
                disabled={!input.trim()}
                className="send-btn"
              >
                Send
              </button>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

export default ChatbotWidget;