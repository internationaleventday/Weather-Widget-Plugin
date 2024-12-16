<?php
/**
 * Plugin Name: Weather Widget
 * Description: A simple weather widget plugin that fetches data from OpenWeatherMap API.
 * Version: 1.0
 * Author: Your Name
 */

// Add the widget to WordPress
function weather_widget_register() {
    register_widget('Weather_Widget');
}
add_action('widgets_init', 'weather_widget_register');

// Create the Weather Widget class
class Weather_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'weather_widget',
            __('Weather Widget', 'text_domain'),
            array('description' => __('Displays weather information', 'text_domain'))
        );
    }

    // Output the widget content
    public function widget($args, $instance) {
        $city = !empty($instance['city']) ? $instance['city'] : 'London'; // Default city
        $api_key = 'your_api_key'; // Replace with your OpenWeatherMap API key
        $weather_data = $this->get_weather($city, $api_key);
        
        echo $args['before_widget'];
        echo $args['before_title'] . 'Weather in ' . esc_html($city) . $args['after_title'];
        if ($weather_data) {
            echo '<p>' . $weather_data['weather'][0]['description'] . '</p>';
            echo '<p>Temperature: ' . $weather_data['main']['temp'] . '°C</p>';
        } else {
            echo '<p>Unable to retrieve weather data.</p>';
        }
        echo $args['after_widget'];
    }

    // Widget backend form
    public function form($instance) {
        $city = !empty($instance['city']) ? $instance['city'] : __('London', 'text_domain');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('city'); ?>"><?php _e('City:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('city'); ?>" name="<?php echo $this->get_field_name('city'); ?>" type="text" value="<?php echo esc_attr($city); ?>" />
        </p>
        <?php
    }

    // Save the widget's options
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['city'] = !empty($new_instance['city']) ? strip_tags($new_instance['city']) : '';
        return $instance;
    }

    // Get weather data from OpenWeatherMap API
    private function get_weather($city, $api_key) {
        $url = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $api_key . "&units=metric";
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return $data;
    }
}
?>
