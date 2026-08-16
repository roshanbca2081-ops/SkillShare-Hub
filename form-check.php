<?php
// This is just to show what the registration form should have
echo "<h1>Registration Form Structure Check</h1>";
echo "<h2>Expected Form Fields:</h2>";
echo "<ul>";
echo "<li>✅ regFirstName - First Name</li>";
echo "<li>✅ regLastName - Last Name</li>";
echo "<li>✅ regEmail - Email Address</li>";
echo "<li>✅ regRole - Role (fresher/mentor)</li>";
echo "<li>✅ regField - Background Field (optional)</li>";
echo "<li>✅ regCourse - Interested Course (optional)</li>";
echo "<li>✅ regPassword - Password</li>";
echo "<li>✅ regConfirmPassword - Confirm Password</li>";
echo "<li>✅ regTerms - Terms & Privacy</li>";
echo "</ul>";

echo "<h2>Removed Fields:</h2>";
echo "<ul>";
echo "<li>❌ regName (replaced with regFirstName + regLastName)</li>";
echo "<li>❌ regAddress</li>";
echo "<li>❌ regContact</li>";
echo "</ul>";

echo "<h2>API Endpoint: api/register.php</h2>";
echo "<p>Now accepts:</p>";
echo "<pre>";
echo "POST /api/register.php\n";
echo "{\n";
echo "  'firstname': string,\n";
echo "  'lastname': string,\n";
echo "  'email': string,\n";
echo "  'role': 'fresher|mentor',\n";
echo "  'password': string,\n";
echo "  'confirm_password': string\n";
echo "}\n";
echo "</pre>";

echo "<p><a href='../login.php#register'>Go to Registration Form →</a></p>";
?>
