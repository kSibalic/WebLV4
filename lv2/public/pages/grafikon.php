<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../auth/check_session.php';
?>
<!DOCTYPE html>
<html lang="hr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="Karlo Sibalic">
		<meta name="description" content="LV1 iz kolegija Web programiranje">
		<meta name="keywords" content="HTML, CSS, FERIT">
		<link rel="stylesheet" href="../style/style.css">
		<title>LV1 - Web programiranje</title> 
	</head>
<body>
    <nav aria-labelledby="primarna-navigacija">
        <ul>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn">Menu</a>
                <div class="dropdown-content" id="primarna-navigacija">
                    <a href="../index.php">Pocetna</a>
                    <a href="#">Grafikon</a>
                    <a href="slike.php">Galerija</a>
                    <a href="videoteka.php">Videoteka</a>
                    <?php if (is_logged_in()): ?>
                        <a href="../auth/logout.php">Odjava</a>
                    <?php else: ?>
                        <a href="../auth/login.php">Prijava</a>
                        <a href="../auth/register.php">Registracija</a>
                    <?php endif; ?>
                </div>
            </li>
        </ul>
    </nav>

    <div class="data-container">
        <table>
            <tr>
                <th>Temperature (°C)</th>
                <th>Humidity (%)</th>
                <th>Wind Speed (km/h)</th>
                <th>Precipitation (%)</th>
                <th>Cloud Cover</th>
                <th>Atmospheric Pressure</th>
                <th>UV Index</th>
                <th>Season</th>
                <th>Visibility (km)</th>
                <th>Location</th>
                <th>Weather Type</th>
            </tr>
            <tr><td>23</td><td>69</td><td>9.5</td><td>10</td><td>clear</td><td>1012.91</td><td>9</td><td>Spring</td><td>8.5</td><td>inland</td><td>Sunny</td></tr>
            <tr><td>29</td><td>82</td><td>18</td><td>78</td><td>overcast</td><td>991.54</td><td>3</td><td>Spring</td><td>4</td><td>inland</td><td>Rainy</td></tr>
            <tr><td>36</td><td>48</td><td>3.5</td><td>11</td><td>partly cloudy</td><td>1024.92</td><td>10</td><td>Autumn</td><td>5.5</td><td>mountain</td><td>Sunny</td></tr>
            <tr><td>32</td><td>35</td><td>5.5</td><td>9</td><td>partly cloudy</td><td>1010.24</td><td>6</td><td>Summer</td><td>9</td><td>coastal</td><td>Sunny</td></tr>
            <tr><td>23</td><td>23</td><td>3.5</td><td>8</td><td>clear</td><td>1027.69</td><td>5</td><td>Spring</td><td>6.5</td><td>mountain</td><td>Sunny</td></tr>
            <tr><td>30</td><td>57</td><td>12.5</td><td>33</td><td>overcast</td><td>1002.2</td><td>1</td><td>Autumn</td><td>7</td><td>inland</td><td>Cloudy</td></tr>
            <tr><td>28</td><td>65</td><td>1.5</td><td>20</td><td>partly cloudy</td><td>1007</td><td>4</td><td>Spring</td><td>8.5</td><td>mountain</td><td>Cloudy</td></tr>
            <tr><td>29</td><td>96</td><td>11.5</td><td>79</td><td>partly cloudy</td><td>1013.23</td><td>0</td><td>Spring</td><td>1</td><td>inland</td><td>Rainy</td></tr>
            <tr><td>35</td><td>63</td><td>8</td><td>3</td><td>clear</td><td>1029.83</td><td>10</td><td>Spring</td><td>5</td><td>mountain</td><td>Sunny</td></tr>
            <tr><td>18</td><td>82</td><td>8.5</td><td>83</td><td>overcast</td><td>999.13</td><td>0</td><td>Summer</td><td>2.5</td><td>coastal</td><td>Rainy</td></tr>
            <tr><td>8</td><td>90</td><td>1.5</td><td>70</td><td>partly cloudy</td><td>1011.19</td><td>10</td><td>Spring</td><td>7.5</td><td>mountain</td><td>Cloudy</td></tr>
            <tr><td>19</td><td>65</td><td>2.5</td><td>20</td><td>partly cloudy</td><td>1016.81</td><td>1</td><td>Spring</td><td>8</td><td>inland</td><td>Cloudy</td></tr>
            <tr><td>28</td><td>72</td><td>14</td><td>73</td><td>partly cloudy</td><td>1005.12</td><td>1</td><td>Summer</td><td>1</td><td>coastal</td><td>Rainy</td></tr>
            <tr><td>40</td><td>94</td><td>15</td><td>97</td><td>clear</td><td>1021.59</td><td>3</td><td>Autumn</td><td>4.5</td><td>coastal</td><td>Sunny</td></tr>
            <tr><td>15</td><td>61</td><td>11</td><td>63</td><td>overcast</td><td>1006.12</td><td>3</td><td>Spring</td><td>4.5</td><td>mountain</td><td>Rainy</td></tr>
            <tr><td>26</td><td>90</td><td>26</td><td>88</td><td>partly cloudy</td><td>998.97</td><td>4</td><td>Summer</td><td>2.5</td><td>coastal</td><td>Rainy</td></tr>
            <tr><td>26</td><td>67</td><td>11</td><td>74</td><td>overcast</td><td>993.37</td><td>1</td><td>Spring</td><td>1.5</td><td>inland</td><td>Rainy</td></tr>
        </table>
    </div>

    <!-- Tortni grafikon -->
    <div class="analysis-flex">
        <div class="analysis-box">
            <div class="pie-chart">
                <div class="segment-tooltip" style="top: 10%; left: 70%;">
                    Rainy: 7 days (41.18%)
                </div>
                <div class="segment-tooltip" style="top: 15%; right: 40%;">
                    Cloudy: 4 days (23.53%)
                </div>
                <div class="segment-tooltip" style="bottom: 5%; left: -20%;">
                    Sunny: 6 days (35.29%)
                </div>
            </div>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #2ecc71;"></div>
                    <span>Sunny</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #f1c40f;"></div>
                    <span>Cloudy</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #3498db;"></div>
                    <span>Rainy</span>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html> 