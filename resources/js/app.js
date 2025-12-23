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

Alpine.start();
