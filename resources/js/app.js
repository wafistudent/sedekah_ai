import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Network tree component for displaying MLM hierarchy
 * 
 * @returns {Object} Alpine component
 */
window.networkTreeComponent = function() {
    return {
        showModal: false,
        member: {},
        /**
         * Show member detail modal
         * 
         * @param {string} memberId - The member ID to fetch details for
         * @returns {Promise<void>}
         */
        async showMemberDetail(memberId) {
            try {
                const response = await fetch(`/api/members/${memberId}`);
                if (!response.ok) throw new Error('Failed to fetch');
                this.member = await response.json();
                this.showModal = true;
            } catch (error) {
                console.error(error);
                alert('Failed to load member details');
            }
        }
    };
};

/**
 * Register member form validation component
 * 
 * @returns {Object} Alpine component
 */
window.registerMemberForm = function() {
    return {
        form: { username: '', email: '', password: '' },
        errors: {},
        /**
         * Validate username field
         * 
         * @returns {void}
         */
        validateUsername() {
            if (this.form.username.length < 3 || this.form.username.length > 20) {
                this.errors.username = 'Username must be 3-20 characters';
            } else {
                delete this.errors.username;
            }
        },
        /**
         * Validate email field
         * 
         * @returns {void}
         */
        validateEmail() {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regex.test(this.form.email)) {
                this.errors.email = 'Invalid email format';
            } else {
                delete this.errors.email;
            }
        },
        /**
         * Validate password field
         * 
         * @returns {void}
         */
        validatePassword() {
            if (this.form.password.length < 8) {
                this.errors.password = 'Password min 8 characters';
            } else {
                delete this.errors.password;
            }
        },
        /**
         * Check if form has validation errors
         * 
         * @returns {boolean}
         */
        hasErrors() {
            return Object.keys(this.errors).length > 0;
        }
    };
};

/**
 * WhatsApp Editor component for template creation/editing
 * 
 * @returns {Object} Alpine component
 */
window.whatsappEditor = function() {
    return {
        content: '',
        category: 'member',
        availableVariables: {
            member: ['name', 'username', 'email', 'phone'],
            commission: ['name', 'username', 'email', 'phone', 'amount', 'commission_type'],
            withdrawal: ['name', 'username', 'email', 'phone', 'amount', 'status'],
            general: ['name', 'username', 'email', 'phone']
        },
        variablesValid: true,
        
        /**
         * Insert variable at cursor position
         * 
         * @param {string} variable - Variable name to insert
         * @returns {void}
         */
        insertVariable(variable) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const before = text.substring(0, start);
            const after = text.substring(end, text.length);
            
            this.content = before + '{{' + variable + '}}' + after;
            textarea.value = this.content;
            
            // Set cursor position after inserted variable
            const newPos = start + variable.length + 4;
            textarea.setSelectionRange(newPos, newPos);
            textarea.focus();
            
            this.updatePreview();
        },
        
        /**
         * Wrap selected text with formatting symbols
         * 
         * @param {string} symbol - Format symbol (*bold*, _italic_, ~strike~, ```mono```)
         * @returns {void}
         */
        wrapText(symbol) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selectedText = text.substring(start, end);
            
            if (selectedText) {
                const before = text.substring(0, start);
                const after = text.substring(end, text.length);
                this.content = before + symbol + selectedText + symbol + after;
                textarea.value = this.content;
                
                // Restore selection
                textarea.setSelectionRange(start, end + (symbol.length * 2));
                textarea.focus();
                
                this.updatePreview();
            }
        },
        
        /**
         * Insert emoji at cursor position
         * 
         * @param {string} emoji - Emoji to insert
         * @returns {void}
         */
        insertEmoji(emoji) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const before = text.substring(0, start);
            const after = text.substring(end, text.length);
            
            this.content = before + emoji + after;
            textarea.value = this.content;
            
            // Set cursor position after emoji
            const newPos = start + emoji.length;
            textarea.setSelectionRange(newPos, newPos);
            textarea.focus();
            
            this.updatePreview();
        },
        
        /**
         * Update preview with current content
         * 
         * @returns {void}
         */
        updatePreview() {
            this.$dispatch('preview-update', { content: this.content });
        }
    };
};

/**
 * WhatsApp Preview component
 * 
 * @param {string} initialContent - Initial content to display
 * @param {Object} dummyData - Dummy data for variable replacement
 * @returns {Object} Alpine component
 */
window.whatsappPreview = function(initialContent = '', dummyData = {}) {
    return {
        content: initialContent,
        parsedContent: '',
        dummyData: {
            name: 'John Doe',
            username: 'johndoe',
            email: 'john@example.com',
            phone: '+62812345678',
            amount: 'Rp 100,000',
            commission_type: 'Direct',
            status: 'Approved',
            ...dummyData
        },
        
        init() {
            this.parseContent();
            
            // Listen for content updates
            this.$watch('content', () => {
                this.parseContent();
            });
            
            // Listen for preview update events
            window.addEventListener('preview-update', (e) => {
                this.content = e.detail.content;
                this.parseContent();
            });
        },
        
        /**
         * Parse content - replace variables and markdown
         * 
         * @returns {void}
         */
        parseContent() {
            let parsed = this.content;
            
            // Replace variables with dummy data
            Object.keys(this.dummyData).forEach(key => {
                const regex = new RegExp(`{{${key}}}`, 'g');
                parsed = parsed.replace(regex, this.dummyData[key]);
            });
            
            // Parse markdown formatting
            parsed = this.parseMarkdown(parsed);
            
            this.parsedContent = parsed;
        },
        
        /**
         * Convert WhatsApp markdown to HTML
         * 
         * @param {string} text - Text to parse
         * @returns {string} Parsed HTML
         */
        parseMarkdown(text) {
            // Bold: *text*
            text = text.replace(/\*([^*]+)\*/g, '<strong>$1</strong>');
            
            // Italic: _text_
            text = text.replace(/_([^_]+)_/g, '<em>$1</em>');
            
            // Strikethrough: ~text~
            text = text.replace(/~([^~]+)~/g, '<del>$1</del>');
            
            // Monospace: ```text```
            text = text.replace(/```([^`]+)```/g, '<code>$1</code>');
            
            return text;
        }
    };
};

/**
 * Test Send Modal component
 * 
 * @returns {Object} Alpine component
 */
window.testSendModal = function() {
    return {
        show: false,
        phone: '',
        testData: {
            name: 'John Doe',
            username: 'johndoe',
            email: 'john@example.com',
            phone: '+62812345678',
            amount: 'Rp 100,000',
            commission_type: 'Direct',
            status: 'Approved'
        },
        loading: false,
        parsedContent: '',
        
        init() {
            // Listen for modal open events
            window.addEventListener('open-test-modal', () => {
                this.show = true;
            });
        },
        
        /**
         * Send test message via AJAX
         * 
         * @returns {Promise<void>}
         */
        async sendTest() {
            if (!this.phone) {
                alert('Please enter a phone number');
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('/admin/whatsapp/templates/test-send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        phone: this.phone,
                        test_data: this.testData,
                        content: document.getElementById('content')?.value || ''
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Test message sent successfully!');
                    this.show = false;
                } else {
                    alert('Failed to send test message: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error sending test message:', error);
                alert('Failed to send test message. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    };
};

/**
 * WhatsApp Logs component with bulk actions
 * 
 * @returns {Object} Alpine component
 */
window.whatsappLogs = function() {
    return {
        selectedLogs: [],
        selectAll: false,
        
        /**
         * Toggle all checkboxes
         * 
         * @returns {void}
         */
        toggleAll() {
            if (this.selectAll) {
                // Select all visible log IDs
                const checkboxes = document.querySelectorAll('input[type="checkbox"][x-model="selectedLogs"]');
                this.selectedLogs = Array.from(checkboxes).map(cb => parseInt(cb.value));
            } else {
                this.selectedLogs = [];
            }
        },
        
        /**
         * Bulk resend selected logs
         * 
         * @returns {Promise<void>}
         */
        async bulkResend() {
            if (this.selectedLogs.length === 0) {
                alert('Please select logs to resend');
                return;
            }
            
            if (!confirm(`Resend ${this.selectedLogs.length} message(s)?`)) {
                return;
            }
            
            try {
                const response = await fetch('/admin/whatsapp/logs/bulk-resend', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        log_ids: this.selectedLogs
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(`Successfully queued ${result.count} message(s) for resending`);
                    window.location.reload();
                } else {
                    alert('Failed to resend messages: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error resending messages:', error);
                alert('Failed to resend messages. Please try again.');
            }
        }
    };
};

/**
 * Toast Notification component
 * 
 * @returns {Object} Alpine component
 */
window.toastNotification = function() {
    return {
        show: false,
        type: 'info',
        message: '',
        
        init() {
            // Listen for toast events
            window.addEventListener('show-toast', (e) => {
                this.type = e.detail.type || 'info';
                this.message = e.detail.message || '';
                this.show = true;
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    this.show = false;
                }, 5000);
            });
        },
        
        get bgClass() {
            const classes = {
                success: 'bg-green-50 border-green-200',
                error: 'bg-red-50 border-red-200',
                warning: 'bg-yellow-50 border-yellow-200',
                info: 'bg-blue-50 border-blue-200'
            };
            return classes[this.type] || classes.info;
        }
    };
};

Alpine.start();
