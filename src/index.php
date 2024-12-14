<?php
echo "<h1>Welcome to the Turkish Travel Advisory Website</h1>";
echo "<p>This is the home page where travel news will be displayed.</p>";

// Navigation Menu
echo "<nav>";
echo "<ul>";
echo "<li><a href='login.php'>Login</a></li>";
echo "<li><a href='profile.php'>Profile</a></li>";
echo "<li><a href='comment.php'>Post Comments</a></li>";
echo "<li><a href='search.php'>Search Travel Advisories</a></li>";
echo "</ul>";
echo "</nav>";

// Styling for the navigation menu
echo "<style>
    nav ul {
        list-style-type: none;
        padding: 0;
    }
    nav ul li {
        display: inline;
        margin-right: 20px;
    }
    nav ul li a {
        text-decoration: none;
        color: blue;
        font-weight: bold;
    }
    nav ul li a:hover {
        color: darkblue;
    }
</style>";
?>