<?php
echo "<h1>Search for Travel Advisories</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = htmlspecialchars($_POST['country']);
    echo "<p>Searching for travel advisories related to: $country</p>";
}
?>

<form method="POST">
    <input type="text" name="country" placeholder="Enter a country" required>
    <button type="submit">Search</button>
</form>
