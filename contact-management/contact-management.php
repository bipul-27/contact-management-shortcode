<?php
/*
Plugin Name: Contact Management
Description: A simple contact management system with shortcode.
Version: 1.0
Author:Bipul Karmokar
*/

if (!defined('ABSPATH')) {
    exit;
}

class ContactManagement {
    public function __construct() {
        add_action('init', array($this, 'create_contact_table'));
        add_shortcode('contact_management', array($this, 'shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_add_contact', array($this, 'add_contact'));
        add_action('wp_ajax_nopriv_add_contact', array($this, 'add_contact'));
        add_action('wp_ajax_get_contacts', array($this, 'get_contacts'));
        add_action('wp_ajax_nopriv_get_contacts', array($this, 'get_contacts'));
        add_action('wp_ajax_edit_contact', array($this, 'edit_contact'));
        add_action('wp_ajax_nopriv_edit_contact', array($this, 'edit_contact'));
        add_action('wp_ajax_delete_contact', array($this, 'delete_contact'));
        add_action('wp_ajax_nopriv_delete_contact', array($this, 'delete_contact'));
    }

    public function create_contact_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(15) NOT NULL,
            gender varchar(10) NOT NULL,
            designation varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function shortcode() {
        ob_start();
        ?>
        <div class="contact-management">
            <div id="contact-form">
                <h2>Contact Form</h2>
                <input type="text" id="name" placeholder="Name" required>
                <span id="name-error" class="error-message">Name is required</span>
                <input type="email" id="email" placeholder="Email" required>
                <span id="email-error" class="error-message">Email is required</span>
                <input type="tel" id="phone" placeholder="Phone" required>
                <span id="phone-error" class="error-message">Phone is required</span>
                <select id="gender">
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" id="designation" placeholder="Designation">
                <button onclick="addContact()">Add Contact</button>
            </div>
            <div id="edit-form" style="display:none;">
            <h2>Edit Contact</h2>
            <input type="hidden" id="edit-id">
            <input type="text" id="edit-name" placeholder="Name" required>
            <span id="edit-name-error" class="error-message">Name is required</span>
            <input type="email" id="edit-email" placeholder="Email" required>
            <span id="edit-email-error" class="error-message">Email is required</span>
            <input type="tel" id="edit-phone" placeholder="Phone" required>
            <span id="edit-phone-error" class="error-message">Phone is required</span>
            <select id="edit-gender">
                <option value="" disabled>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" id="edit-designation" placeholder="Designation">
            <button onclick="updateContact()">Update Contact</button>
        </div>
        <div id="contact-list">
    <h2>Contact List</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Designation</th>
            <th>Actions</th>
        </tr>
        <tbody id="contacts"></tbody>
    </table>
</div>
            <div id="success-message" class="success-message">Contact added successfully!</div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function enqueue_scripts() {
        wp_enqueue_style('contact-management-style', plugins_url('style.css', __FILE__));
        wp_enqueue_script('contact-management-script', plugins_url('script.js', __FILE__), array('jquery'), null, true);

        wp_localize_script('contact-management-script', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }

    public function add_contact() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts';
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $gender = sanitize_text_field($_POST['gender']);
        $designation = sanitize_text_field($_POST['designation']);

        $wpdb->insert($table_name, array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'designation' => $designation
        ));

        wp_send_json_success();
    }

    public function get_contacts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts';
        $results = $wpdb->get_results("SELECT * FROM $table_name");

        wp_send_json_success($results);
    }

    public function edit_contact() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts';
        $id = intval($_POST['id']);
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $gender = sanitize_text_field($_POST['gender']);
        $designation = sanitize_text_field($_POST['designation']);

        $wpdb->update($table_name, array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'designation' => $designation
        ), array('id' => $id));

        wp_send_json_success();
    }

    public function delete_contact() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts';
        $id = intval($_POST['id']);

        $wpdb->delete($table_name, array('id' => $id));

        wp_send_json_success();
    }
}

new ContactManagement();
?>
