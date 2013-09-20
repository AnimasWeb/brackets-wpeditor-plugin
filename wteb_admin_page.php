<?php
$keys = wteb_get_keys();

echo '<div class="wrap">';
echo '<div id="icon-themes" class="icon32"><br></div>';
echo '<h2>Brackets Editor</h2>';
echo '<h3>AJAX URL</h3>';
echo '<pre>' . admin_url('admin-ajax.php') . '</pre>';
echo '<h3>KEYS</h3>';
if(empty($keys)) {
  echo 'No keys created!';
} else {
  echo '<table border="1">';
  foreach($keys as $name => $key) {
    echo "<tr><td>$name</td><td style=\"max-width: 400px; word-wrap: break-word;\">$key</td><td><a onclick=\"wteb_delete_key('$name')\">Delete</a></td></tr>";
  }
  echo '</table>';
  echo '<script>';
  echo '  function wteb_delete_key(name) {';
  echo '    if(confirm("Delete the key: " + name)) {';
  echo '      var success = function () { window.location.reload(true); };';
  echo '      jQuery.post("' . admin_url('admin-ajax.php') . '", { action: "wteb_delete_key", name: name }, success);';
  echo '    }';
  echo '  }';
  echo '</script>';
}
echo '<h3>ADD KEY</h3>';
echo '<form method="POST">';
echo '<input type="hidden" name="form_action" value="wteb_add_key" />';
echo 'Name <input type="text" name="name" />';
echo '<input type="submit" value="Add" /><br />';
echo 'Examples: "Work Desktop" or "John\'s Laptop"<br />';
echo '</form>';
echo '</div>';