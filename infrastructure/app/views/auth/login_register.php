<?php define("ASSET_URL", "/final-project/infrastructure/public"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/LoginRegister.css" />
    <title>Login/Register</title>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-logo">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
            </div>
            <h1 class="auth-title">Club&Event Seeker</h1>
        </div>

        <div class="card" id="login-section">
            <form action="/login" method="POST">
                <div class="form-group">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_ID" class="form-input" placeholder="e.g. 36071069" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem; padding: 0.75rem;">Sign In</button>
            </form>
            <div class="auth-footer">
                New student? <a class="auth-link" id="toggle-to-register">Create an account</a>
            </div>
        </div>

        <div class="card hidden" id="register-section">
            <form action="/register" method="POST">
                <div style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">First Name</label>
                        <input type="text" name="firstname" class="form-input" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="lastname" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_ID" class="form-input" required>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-input" required>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Major</label>
                    <select name="major" class="form-select" required>
                        <option value="" disabled selected>Select your major...</option>
                        <option value="ICE">Informatics and Computer Engineering</option>
                        <option value="BDA">Business and Data Analysis</option>
                        <option value="MIS">Management Information System</option>
                        <option value="AC">Accounting, Analyzing and Auditing</option>
                        <option value="AAI">Automation and Informatics</option>
                        <option value="EL">English Language</option>
                        <option value="DC">Digital Communication</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem; padding: 0.75rem;">Create Account</button>
            </form>
            <div class="auth-footer">
                Already have an account? <a class="auth-link" id="toggle-to-login">Sign in</a>
            </div>
        </div>
    </div>

    <script src="<?= ASSET_URL ?>/assets/js/LoginRegister.js"></script>
    
</body>
</html>