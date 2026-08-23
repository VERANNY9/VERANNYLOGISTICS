<?php
session_start();

$users_file = 'users.json';
$staff_file = 'staff.json';
$finance_file = 'finance.json';
$orders_file = 'orders.json';
$meetings_file = 'meetings.json';
$trips_file = 'trips.json';
$leaders_file = 'leaders.json'; 
$settings_file = 'settings.json';

// Hakikisha folda ya uploads ipo
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Function ya kusasisha Home Page moja kwa moja (Sync to leaders.json)
function updateLeadersJson($staff_list, $leaders_file) {
    $leaders = [];
    foreach ($staff_list as $st) {
        $leaders[] = [
            "name" => $st['full_name'] ?? '',
            "role" => $st['role_title'] ?? '',
            "bio" => $st['bio'] ?? '',
            "image" => !empty($st['photo']) ? $st['photo'] : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'
        ];
    }
    file_put_contents($leaders_file, json_encode($leaders, JSON_PRETTY_PRINT));
}

// Kuandaa mipangilio ya awali ya video
if (!file_exists($settings_file)) {
    file_put_contents($settings_file, json_encode(['home_video' => 'video.mp4'], JSON_PRETTY_PRINT));
}
$site_settings = json_decode(file_get_contents($settings_file), true);

// Kuandaa Default CEO na Watumishi wa Awali 
if (!file_exists($staff_file)) {
    $default_staff = [
        ['id' => 1, 'full_name' => 'Nelius Binamungu', 'role_title' => 'Chief Executive Officer (CEO)', 'bio' => 'Anasimamia mikakati na uendeshaji mkuu wa kampuni.', 'photo' => 'logo.jpeg'],
        ['id' => 2, 'full_name' => 'Alistides Steven', 'role_title' => 'Managing Director', 'bio' => 'Usimamizi wa shughuli za kila siku na rasilimali.', 'photo' => 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'],
        ['id' => 3, 'full_name' => 'Stella Jonathani', 'role_title' => 'Msimamizi wa Fedha na Utawala', 'bio' => 'Usimamizi wa kumbukumbu na utawala wa ofisi.', 'photo' => 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'],
        ['id' => 4, 'full_name' => 'Nassoro Juma (Mahengo)', 'role_title' => 'Afisa Masoko na Dereva Mkuu', 'bio' => 'Uratibu wa masoko na wateja. Mawasiliano: +255 652 009 916', 'photo' => 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'],
        ['id' => 5, 'full_name' => 'Livinus Mwijage', 'role_title' => 'Muhasibu', 'bio' => 'Anasimamia masuala yote ya fedha, mapato na matumizi.', 'photo' => 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'],
        ['id' => 6, 'full_name' => 'Eliah Gugah', 'role_title' => 'Mshauri wa Kampuni', 'bio' => 'Anatoa ushauri wa kitaalamu wa kisheria na uwekezaji.', 'photo' => 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png']
    ];
    file_put_contents($staff_file, json_encode($default_staff, JSON_PRETTY_PRINT));
    updateLeadersJson($default_staff, $leaders_file);
}

if (!file_exists($users_file)) {
    $default_users = [
        [
            'id' => 1,
            'first_name' => 'Nelius', 
            'last_name' => 'Binamungu', 
            'email' => 'admin@verannylogistics.com', 
            'password' => password_hash('Buhembe@12', PASSWORD_DEFAULT),
            'role' => 'admin',
            'title' => 'Chief Executive Officer (CEO)',
            'photo' => 'logo.jpeg'
        ]
    ];
    file_put_contents($users_file, json_encode($default_users, JSON_PRETTY_PRINT));
}

if (!file_exists($meetings_file)) { file_put_contents($meetings_file, json_encode([], JSON_PRETTY_PRINT)); }
if (!file_exists($trips_file)) { file_put_contents($trips_file, json_encode([], JSON_PRETTY_PRINT)); }

$users_list = json_decode(file_get_contents($users_file), true);
$staff_list = json_decode(file_get_contents($staff_file), true);
$meetings_list = json_decode(file_get_contents($meetings_file), true);
$trips_list = json_decode(file_get_contents($trips_file), true);

$users_updated = false;
foreach ($users_list as &$usr) {
    if (!isset($usr['id'])) {
        $usr['id'] = time() + mt_rand(1, 1000);
        $users_updated = true;
    }
}
unset($usr);
if ($users_updated) {
    file_put_contents($users_file, json_encode($users_list, JSON_PRETTY_PRINT));
}

$auth_page = $_GET['auth'] ?? 'login';
$auth_error = "";
$auth_success = "";

$ceo_photo_default = 'logo.jpeg';
foreach ($staff_list as $st) {
    if (stripos($st['role_title'] ?? '', 'CEO') !== false || stripos($st['full_name'] ?? '', 'Nelius') !== false) {
        if (!empty($st['photo'])) { $ceo_photo_default = $st['photo']; }
        break;
    }
}

// Orodha Rasmi ya Vyeo
$role_titles = [ 
    'admin' => 'Chief Executive Officer (CEO)',
    'md' => 'Managing Director', 
    'manager' => 'Manager', 
    'accountant' => 'Mhasibu', 
    'assistant_accountant' => 'Mhasibu Msaidizi',
    'advisor' => 'Mshauri wa Kampuni', 
    'driver' => 'Afisa Masoko na Dereva Mkuu',
    'assistant_driver' => 'Dereva Msaidizi'
];

// 1. KUSHUGHULIKIA LOGIN
if (isset($_POST['login_btn'])) {
    $identifier = trim($_POST['identifier'] ?? ''); 
    $password = $_POST['password'] ?? '';

    if ($identifier === 'VerannyLogistics' && $password === 'Buhembe@12') {
        $_SESSION['ceo_logged'] = true;
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Nelius Binamungu';
        $_SESSION['user_role'] = 'admin'; 
        $_SESSION['user_title'] = 'Chief Executive Officer (CEO)';
        $_SESSION['user_photo'] = $ceo_photo_default;
        header("Location: admin.php");
        exit();
    }

    $found = false;
    foreach ($users_list as $usr) {
        if (($usr['email'] === $identifier || ($usr['first_name'] ?? '') === $identifier) && password_verify($password, $usr['password'])) {
            $_SESSION['ceo_logged'] = true;
            $_SESSION['user_id'] = $usr['id'] ?? time();
            $_SESSION['user_name'] = ($usr['first_name'] ?? 'Mtumiaji') . ' ' . ($usr['last_name'] ?? '');
            $_SESSION['user_role'] = $usr['role'] ?? 'manager'; 
            $_SESSION['user_title'] = $usr['title'] ?? ($role_titles[$usr['role'] ?? ''] ?? 'Msimamizi');
            $_SESSION['user_photo'] = !empty($usr['photo']) ? $usr['photo'] : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
            $found = true;
            break;
        }
    }
    if ($found) { header("Location: admin.php"); exit(); } else { $auth_error = "Barua pepe au nenosiri si sahihi!"; }
}

// 2. KUSHUGHULIKIA KUJISAJILI
if (isset($_POST['register_btn'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $pwd = $_POST['password'] ?? '';
    $cpwd = $_POST['confirm_password'] ?? '';
    $selected_role = $_POST['register_role'] ?? 'manager';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $auth_error = "Tafadhali weka Barua Pepe iliyo halali!"; $auth_page = 'register';
    } elseif ($pwd !== $cpwd) { $auth_error = "Nenosiri hazifanani!"; $auth_page = 'register';
    } else {
        $exists = false;
        foreach ($users_list as $usr) { if (($usr['email'] ?? '') === $email) { $exists = true; break; } }

        if ($exists) { $auth_error = "Barua pepe hii imeshajisajiliwa tayari!"; $auth_page = 'register';
        } else {
            $user_title = $role_titles[$selected_role] ?? 'Msimamizi';
            $user_photo = 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
            if (!empty($_FILES['reg_photo']['name'])) {
                $p_name = 'user_' . time() . '_' . basename($_FILES['reg_photo']['name']);
                if (move_uploaded_file($_FILES['reg_photo']['tmp_name'], 'uploads/' . $p_name)) { $user_photo = 'uploads/' . $p_name; }
            }

            $new_user = [ 'id' => time(), 'first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'password' => password_hash($pwd, PASSWORD_DEFAULT), 'role' => $selected_role, 'title' => $user_title, 'photo' => $user_photo ];
            array_unshift($users_list, $new_user);
            file_put_contents($users_file, json_encode($users_list, JSON_PRETTY_PRINT));

            $new_staff_member = [ 'id' => $new_user['id'], 'full_name' => $first_name . ' ' . $last_name, 'role_title' => $user_title, 'bio' => 'Mtumiaji na msimamizi.', 'photo' => $user_photo ];
            array_unshift($staff_list, $new_staff_member);
            file_put_contents($staff_file, json_encode($staff_list, JSON_PRETTY_PRINT));
            
            updateLeadersJson($staff_list, $leaders_file);
            $auth_success = "Akaunti imetengenezwa! Sasa unaweza kuingia.";
            $auth_page = 'login';
        }
    }
}

if (isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit(); }

if (!isset($_SESSION['ceo_logged'])) {
    ?>
    <!DOCTYPE html>
    <html lang="sw">
    <head>
        <meta charset="UTF-8">
        <title>Portal Authentication - Veranny Logistics</title>
        <style>
            body { background: #1b2a47; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
            .login-box { background: white; padding: 35px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); width: 420px; text-align: center; }
            .login-box h2 { color: #1b2a47; margin-bottom: 15px; }
            input, select { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 14px; }
            button { background: #e74c3c; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; font-weight: bold; cursor: pointer; margin-top: 10px; font-size: 14px; }
            button:hover { background: #c0392b; }
            .error, .success { padding: 10px; border-radius: 4px; font-size: 13px; margin-bottom: 15px; text-align: left; }
            .error { background: #f8d7da; color: #721c24; } .success { background: #d4edda; color: #155724; }
            .links { margin-top: 20px; display: flex; justify-content: space-between; font-size: 13px; }
            .links a { color: #1b2a47; text-decoration: none; } .links a:hover { text-decoration: underline; color: #e74c3c; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2><?php echo ($auth_page == 'register') ? "Fungua Akaunti Mpya" : "Ingia Kwenye Mfumo"; ?></h2>
            <?php if(!empty($auth_error)) echo "<div class='error'>$auth_error</div>"; ?>
            <?php if(!empty($auth_success)) echo "<div class='success'>$auth_success</div>"; ?>
            <?php if($auth_page == 'login'): ?>
                <form method="POST">
                    <input type="text" name="identifier" placeholder="Weka Email au Jina la Kwanza" required>
                    <input type="password" name="password" placeholder="Nenosiri" required>
                    <button type="submit" name="login_btn">Ingia</button>
                </form>
                <div class="links"><a href="index.php">&#8592; Rudi Home</a><a href="admin.php?auth=register">Tengeneza Akaunti</a></div>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="first_name" placeholder="Jina la Kwanza" required>
                    <input type="text" name="last_name" placeholder="Jina la Mwisho" required>
                    <input type="email" name="email" placeholder="Barua Pepe (Email)" required>
                    <select name="register_role" required>
                        <option value="admin">Chief Executive Officer (CEO)</option>
                        <option value="manager">Manager</option>
                        <option value="md">Managing Director (MD)</option>
                        <option value="accountant">Mhasibu (Accountant)</option>
                        <option value="assistant_accountant">Mhasibu Msaidizi (Assistant Accountant)</option>
                        <option value="advisor">Mshauri (Advisor)</option>
                        <option value="driver">Afisa Masoko na Dereva Mkuu</option>
                        <option value="assistant_driver">Dereva Msaidizi (Assistant Driver)</option>
                    </select>
                    <label style="text-align: left; display:block; font-size:12px; font-weight:bold; color:#555; margin-top:5px;">Picha ya Wasifu:</label>
                    <input type="file" name="reg_photo" accept="image/*">
                    <input type="password" name="password" placeholder="Nenosiri" required>
                    <input type="password" name="confirm_password" placeholder="Thibitisha Nenosiri" required>
                    <button type="submit" name="register_btn">Jisajili Rasmi</button>
                </form>
                <div class="links" style="justify-content: center;"><a href="admin.php?auth=login">&#8592; Rudi Kwenye Login</a></div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php exit();
}

// DATA MANAGEMENT
if (!file_exists($finance_file)) {
    $default_finance = [ 'cash_flow' => 26800000, 'investment_value' => 65000000, 'savings' => 10000000, 'transactions' => [] ];
    file_put_contents($finance_file, json_encode($default_finance, JSON_PRETTY_PRINT));
}

$finance_data = json_decode(file_get_contents($finance_file), true);
$msg = "";

$user_role = $_SESSION['user_role'] ?? 'manager';
$user_id = $_SESSION['user_id'] ?? 1;
$_SESSION['user_photo'] = $_SESSION['user_photo'] ?? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
$_SESSION['user_title'] = $_SESSION['user_title'] ?? 'Msimamizi';

$is_ceo = ($user_role === 'admin');
$is_md_or_manager = ($user_role === 'md' || $user_role === 'manager');
$is_driver_role = ($user_role === 'driver' || $user_role === 'assistant_driver');
$is_accountant_role = ($user_role === 'accountant' || $user_role === 'assistant_accountant');

// CEO KUHARIRI (EDIT) MTUMISHI / MTUMIAJI KWENYE ORODHA YA WALIOJISAJILI
if ($is_ceo && isset($_POST['ceo_update_user'])) {
    $u_id = intval($_POST['user_id'] ?? 0);
    $fname = trim($_POST['edit_fname'] ?? '');
    $lname = trim($_POST['edit_lname'] ?? '');
    $email = trim($_POST['edit_email'] ?? '');
    $role = $_POST['edit_role'] ?? 'manager';
    
    $title = $role_titles[$role] ?? 'Msimamizi';
    
    foreach ($users_list as &$u) {
        if (intval($u['id'] ?? 0) === $u_id) {
            $u['first_name'] = $fname;
            $u['last_name'] = $lname;
            $u['email'] = $email;
            $u['role'] = $role;
            $u['title'] = $title;
            break;
        }
    }
    unset($u);
    file_put_contents($users_file, json_encode($users_list, JSON_PRETTY_PRINT));
    
    foreach ($staff_list as &$st) {
        if (intval($st['id'] ?? 0) === $u_id) {
            $st['full_name'] = $fname . ' ' . $lname;
            $st['role_title'] = $title;
            break;
        }
    }
    unset($st);
    file_put_contents($staff_file, json_encode($staff_list, JSON_PRETTY_PRINT));
    updateLeadersJson($staff_list, $leaders_file);
    
    $msg = "Taarifa za mtumishi zimerejeshwa na kusasishwa kwa mafanikio!";
}

// DEREVA / DEREVA MSAIDIZI KUWEKA MAONI KWENYE ROUTE
if ($is_driver_role && isset($_POST['submit_driver_trip_comment'])) {
    $t_id = intval($_POST['trip_id'] ?? 0);
    $d_comment = trim($_POST['driver_comment'] ?? '');
    foreach ($trips_list as &$tr) {
        if (intval($tr['id'] ?? 0) === $t_id) {
            $tr['driver_comment'] = $d_comment;
            break;
        }
    }
    unset($tr);
    file_put_contents($trips_file, json_encode($trips_list, JSON_PRETTY_PRINT));
    $msg = "Maoni yako yamewasilishwa kwa uongozi kuhusu route hii!";
}

// CEO ONGEZA RATIBA / ROUTE YA MZIGO MCHANGANYIKO
if ($is_ceo && isset($_POST['ceo_add_mixed_trip'])) {
    $new_trip = [ 
        'id' => time(), 
        'date' => date('d M Y'), 
        'route' => trim($_POST['route_name'] ?? ''), 
        'cargo' => trim($_POST['cargo_details'] ?? ''), 
        'driver' => trim($_POST['driver_assigned'] ?? ''), 
        'revenue' => floatval($_POST['est_revenue'] ?? 0), 
        'status' => 'Route Mpya - Fuata Hii',
        'driver_comment' => ''
    ];
    array_unshift($trips_list, $new_trip);
    file_put_contents($trips_file, json_encode($trips_list, JSON_PRETTY_PRINT));
    $msg = "Ratiba na mzigo vimewekwa! Taarifa imetumwa kwa madereva.";
}

// CEO: KUPAKIA VIDEO MPYA YA HOME PAGE
if ($is_ceo && isset($_POST['update_home_video'])) {
    if (!empty($_FILES['new_home_video']['name'])) {
        $v_name = 'home_vid_' . time() . '_' . basename($_FILES['new_home_video']['name']);
        $v_target = 'uploads/' . $v_name;
        if (move_uploaded_file($_FILES['new_home_video']['tmp_name'], $v_target)) {
            $site_settings['home_video'] = $v_target;
            file_put_contents($settings_file, json_encode($site_settings, JSON_PRETTY_PRINT));
            $msg = "Video ya Home Page imesasishwa kikamilifu!";
        }
    }
}

// CEO: ORODHA YA WALIOJISAJILI -> ONGEZA MTUMISHI MPYA
if ($is_ceo && isset($_POST['ceo_add_new_user'])) {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'manager';
    $bio = trim($_POST['bio'] ?? 'Mtumishi wa Veranny Logistics.');
    $pass = $_POST['password'] ?? '123456';
    
    $title = $role_titles[$role] ?? 'Msimamizi';
    
    $u_photo = 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
    if (!empty($_FILES['u_photo']['name'])) {
        $p_name = 'user_' . time() . '_' . basename($_FILES['u_photo']['name']);
        if (move_uploaded_file($_FILES['u_photo']['tmp_name'], 'uploads/' . $p_name)) { $u_photo = 'uploads/' . $p_name; }
    }
    
    $new_id = time() + mt_rand(1, 1000);
    $users_list[] = [ 'id' => $new_id, 'first_name' => $fname, 'last_name' => $lname, 'email' => $email, 'password' => password_hash($pass, PASSWORD_DEFAULT), 'role' => $role, 'title' => $title, 'photo' => $u_photo ];
    file_put_contents($users_file, json_encode($users_list, JSON_PRETTY_PRINT));
    
    $staff_list[] = [ 'id' => $new_id, 'full_name' => $fname . ' ' . $lname, 'role_title' => $title, 'bio' => $bio, 'photo' => $u_photo ];
    file_put_contents($staff_file, json_encode($staff_list, JSON_PRETTY_PRINT));
    updateLeadersJson($staff_list, $leaders_file);
    
    $msg = "Mtumishi mpya ameongezwa kikamilifu!";
}

// CEO KUBADILI CHEO, BIO NA PICHA YA MTUMISHI
if ($is_ceo && isset($_POST['update_staff_role'])) {
    $staff_id = intval($_POST['staff_id'] ?? 0);
    $new_role_title = trim($_POST['new_role_title'] ?? '');
    $new_bio = trim($_POST['new_bio'] ?? '');
    
    $photo_updated = false;
    $target_photo = "";
    if (!empty($_FILES['new_staff_photo']['name'])) {
        $p_name = 'staff_' . $staff_id . '_' . time() . '_' . basename($_FILES['new_staff_photo']['name']);
        if (move_uploaded_file($_FILES['new_staff_photo']['tmp_name'], 'uploads/' . $p_name)) {
            $target_photo = 'uploads/' . $p_name;
            $photo_updated = true;
        }
    }

    foreach ($staff_list as &$st) {
        if (intval($st['id'] ?? 0) === $staff_id) {
            if ($new_role_title !== '') $st['role_title'] = $new_role_title;
            if ($new_bio !== '') $st['bio'] = $new_bio;
            if ($photo_updated) $st['photo'] = $target_photo;
            break;
        }
    }
    unset($st);
    file_put_contents($staff_file, json_encode($staff_list, JSON_PRETTY_PRINT));
    
    foreach ($users_list as &$u) {
        if (intval($u['id'] ?? 0) === $staff_id) {
            if ($new_role_title !== '') $u['title'] = $new_role_title;
            if ($photo_updated) $u['photo'] = $target_photo;
            break;
        }
    }
    unset($u);
    file_put_contents($users_file, json_encode($users_list, JSON_PRETTY_PRINT));
    
    updateLeadersJson($staff_list, $leaders_file);
    $msg = "Taarifa na Picha ya mtumishi vimesasishwa!";
}

// Sasisha Picha Yako Binafsi
if (isset($_POST['update_my_photo']) && !empty($_FILES['my_new_photo']['name'])) {
    $p_name = 'user_' . $user_id . '_' . time() . '_' . basename($_FILES['my_new_photo']['name']);
    $target = 'uploads/' . $p_name;
    if (move_uploaded_file($_FILES['my_new_photo']['tmp_name'], $target)) {
        $_SESSION['user_photo'] = $target;
        foreach ($users_list as &$u) { if (($u['id'] ?? 0) == $user_id) { $u['photo'] = $target; } }
        file_put_contents($users_file, json_encode($users_list, JSON_PRETTY_PRINT));
        
        foreach ($staff_list as &$st) { if (($st['id'] ?? 0) == $user_id || strcasecmp($st['full_name'] ?? '', $_SESSION['user_name']) === 0) { $st['photo'] = $target; } }
        unset($st);
        file_put_contents($staff_file, json_encode($staff_list, JSON_PRETTY_PRINT));
        updateLeadersJson($staff_list, $leaders_file);
        $msg = "Picha yako imesasishwa!";
    }
}

// CEO Kufuta Mtumiaji
if ($is_ceo && isset($_GET['delete_user_id'])) {
    $del_uid = intval($_GET['delete_user_id']);
    if ($del_uid !== 1) {
        $users_list = array_values(array_filter($users_list, function($u) use ($del_uid) { return intval($u['id'] ?? 0) !== $del_uid; }));
        file_put_contents($users_file, json_encode($users_list, JSON_PRETTY_PRINT));
        
        $staff_list = array_values(array_filter($staff_list, function($st) use ($del_uid) { return intval($st['id'] ?? 0) !== $del_uid; }));
        file_put_contents($staff_file, json_encode($staff_list, JSON_PRETTY_PRINT));
        updateLeadersJson($staff_list, $leaders_file);
        
        $msg = "Mtumiaji amefutwa!";
        header("Location: admin.php?tab=manage_users");
        exit();
    }
}

// Mshauri
if ($user_role === 'advisor' && isset($_POST['create_meeting'])) {
    $new_meeting = [ 'id' => time(), 'date' => date('d M Y, H:i'), 'title' => trim($_POST['m_title'] ?? ''), 'subject' => trim($_POST['m_subject'] ?? ''), 'description' => trim($_POST['m_desc'] ?? ''), 'advisor_name' => $_SESSION['user_name'] ];
    array_unshift($meetings_list, $new_meeting);
    file_put_contents($meetings_file, json_encode($meetings_list, JSON_PRETTY_PRINT));
    $msg = "Kikao kimeanzishwa!";
}

if ($is_accountant_role && isset($_POST['accountant_submit_calc'])) {
    $amount = floatval($_POST['tx_amount'] ?? 0);
    $doc_path = "";
    if (!empty($_FILES['tx_document']['name'])) {
        $doc_name = 'acc_' . time() . '_' . basename($_FILES['tx_document']['name']);
        if (move_uploaded_file($_FILES['tx_document']['tmp_name'], 'uploads/' . $doc_name)) { $doc_path = 'uploads/' . $doc_name; }
    }
    $new_tx = [ 'id' => time(), 'date' => date('d M Y, H:i'), 'driver_name' => $_SESSION['user_name'] . ' (' . $_SESSION['user_title'] . ')', 'destination' => 'Ofisini / Mahesabu', 'cargo_desc' => trim($_POST['tx_desc'] ?? ''), 'cargo_qty' => 'N/A', 'subject' => trim($_POST['tx_subject'] ?? ''), 'collected_amount' => $amount, 'profit_amount' => $amount, 'amount' => $amount, 'type' => 'accountant_calc', 'receipt_photo' => $doc_path, 'ceo_comment' => '', 'driver_reply' => '', 'accountant_status' => 'pending', 'ceo_seen' => false ];
    array_unshift($finance_data['transactions'], $new_tx);
    file_put_contents($finance_file, json_encode($finance_data, JSON_PRETTY_PRINT));
    $msg = "Mahesabu yamewasilishwa kwa CEO!";
}

if ($is_driver_role && isset($_POST['submit_trip_settlement'])) {
    $destination = trim($_POST['destination'] ?? '');
    $fuel_cost = floatval($_POST['fuel_cost'] ?? 0);
    $other_cost = floatval($_POST['other_cost'] ?? 0);
    $collected_amount = floatval($_POST['collected_amount'] ?? 0);
    $net_revenue = $collected_amount - ($fuel_cost + $other_cost);
    
    $receipt_photo_path = "";
    if (!empty($_FILES['receipt_photo']['name'])) {
        $photo_name = time() . '_' . basename($_FILES['receipt_photo']['name']);
        if (move_uploaded_file($_FILES['receipt_photo']['tmp_name'], 'uploads/' . $photo_name)) { $receipt_photo_path = 'uploads/' . $photo_name; }
    }
    $new_tx = [ 'id' => time(), 'date' => date('d M Y, H:i'), 'driver_name' => $_SESSION['user_name'], 'destination' => $destination, 'cargo_desc' => trim($_POST['cargo_desc'] ?? ''), 'cargo_qty' => trim($_POST['cargo_qty'] ?? ''), 'subject' => 'Safari ya ' . $destination, 'collected_amount' => $collected_amount, 'profit_amount' => floatval($_POST['profit_amount'] ?? 0), 'amount' => $net_revenue, 'type' => 'income_settlement', 'receipt_photo' => $receipt_photo_path, 'ceo_comment' => '', 'driver_reply' => '', 'accountant_status' => 'pending', 'ceo_seen' => false ];
    array_unshift($finance_data['transactions'], $new_tx);
    file_put_contents($finance_file, json_encode($finance_data, JSON_PRETTY_PRINT));
    $msg = "Hesabu za safari zimewasilishwa!";
}

if ($is_ceo && isset($_POST['approve_accountant_tx'])) {
    $tx_id = $_POST['tx_id'] ?? '';
    foreach ($finance_data['transactions'] as &$tx) {
        if (($tx['id'] ?? '') == $tx_id) { $tx['accountant_status'] = 'approved'; $finance_data['cash_flow'] += ($tx['amount'] ?? 0); break; }
    }
    file_put_contents($finance_file, json_encode($finance_data, JSON_PRETTY_PRINT));
    $msg = "Hesabu imepitishwa!";
}

if ($is_ceo && isset($_POST['save_ceo_comment'])) {
    $tx_id = $_POST['tx_id'] ?? '';
    foreach ($finance_data['transactions'] as &$tx) {
        if (($tx['id'] ?? '') == $tx_id) { $tx['ceo_comment'] = trim($_POST['ceo_comment'] ?? ''); $tx['ceo_seen'] = true; break; }
    }
    file_put_contents($finance_file, json_encode($finance_data, JSON_PRETTY_PRINT));
    $msg = "Maoni yametumwa!";
}

$tab = $_GET['tab'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Portal - Veranny Logistics</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { display: flex; background: #f4f6f9; color: #333; height: 100vh; overflow: hidden; }
        .sidebar { width: 260px; background: #1b2a47; color: white; display: flex; flex-direction: column; justify-content: space-between; height: 100vh; }
        .sidebar-top { padding: 20px; overflow-y: auto; }
        .brand-box { text-align: center; margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        .brand-logo { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #e74c3c; margin-bottom: 8px; }
        .brand-title { font-size: 13px; font-weight: bold; color: #fff; letter-spacing: 0.5px; }
        .sidebar h2 { font-size: 11px; text-transform: uppercase; color: #a4b0be; margin-bottom: 15px; text-align: center; }
        .nav-links { list-style: none; }
        .nav-links li a { display: block; padding: 12px 20px; color: #dfe4ea; text-decoration: none; font-size: 14px; transition: 0.3s; border-radius: 4px; margin-bottom: 4px; }
        .nav-links li a:hover, .nav-links li.active a { background: #e74c3c; color: white; }
        .logout-btn { padding: 15px 20px; background: rgba(0,0,0,0.2); }
        .logout-btn a { color: #ff6b6b; text-decoration: none; font-weight: bold; font-size: 14px; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; height: 100vh; }
        .top-bar { background: white; padding: 15px 30px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .ceo-profile-box { display: flex; align-items: center; gap: 12px; }
        .ceo-avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #1b2a47; background: #fff; }
        .ceo-info h4 { font-size: 14px; color: #1b2a47; margin-bottom: 2px; }
        .ceo-info span { font-size: 11px; color: #7f8c8d; background: #ecf0f1; padding: 2px 6px; border-radius: 4px; }
        .panel { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 20px; }
        form label { display: block; margin-top: 15px; font-weight: bold; font-size: 13px; color: #2c3e50; }
        form input, form textarea, form select { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; }
        .btn-submit { background: #e74c3c; color: white; border: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 20px; }
        .btn-edit { background: #2980b9; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 5px; display: inline-block; cursor:pointer; }
        .btn-danger { background: #c0392b; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; display: inline-block; }
        .alert { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #f1f2f6; font-size: 14px; vertical-align: middle; }
        th { background: #1b2a47; color: white; }
        .cards-modern { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        .card-modern { background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 25px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.04); border-top: 5px solid #1b2a47; position: relative; overflow: hidden; }
        .card-modern.accent { border-top-color: #e74c3c; }
        .card-modern.green { border-top-color: #2ecc71; }
        .card-modern h3 { font-size: 13px; text-transform: uppercase; color: #7f8c8d; letter-spacing: 0.5px; margin-bottom: 8px; }
        .card-modern .val { font-size: 26px; font-weight: 800; color: #1b2a47; }
        .badge-pending { background: #f39c12; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-approved { background: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .meeting-card { background: #fff; border-left: 5px solid #2980b9; padding: 15px; margin-bottom: 15px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-top">
            <div class="brand-box">
                <img src="<?php echo $_SESSION['user_photo']; ?>" class="brand-logo">
                <div class="brand-title">VERANNY LOGISTICS</div>
            </div>
            <h2><?php echo $_SESSION['user_title']; ?></h2>
            <ul class="nav-links">
                <?php if ($is_driver_role): ?>
                    <li class="<?php echo ($tab == 'dashboard') ? 'active' : ''; ?>"><a href="admin.php?tab=dashboard">🚛 Funga Hesabu & Risiti</a></li>
                <?php elseif ($is_accountant_role): ?>
                    <li class="<?php echo ($tab == 'dashboard') ? 'active' : ''; ?>"><a href="admin.php?tab=dashboard">📊 Eneo la Mahesabu & Miamala</a></li>
                <?php else: ?>
                    <li class="<?php echo ($tab == 'dashboard') ? 'active' : ''; ?>"><a href="admin.php?tab=dashboard">📊 Muhtasari Mkuu</a></li>
                    
                    <?php if ($is_ceo): ?>
                        <li class="<?php echo ($tab == 'manage_users') ? 'active' : ''; ?>"><a href="admin.php?tab=manage_users">👤 Orodha ya Waliojisajili</a></li>
                        <li class="<?php echo ($tab == 'site_settings') ? 'active' : ''; ?>"><a href="admin.php?tab=site_settings">⚙️ Mpangilio wa Tovuti (Video)</a></li>
                    <?php endif; ?>

                    <li class="<?php echo ($tab == 'staff') ? 'active' : ''; ?>"><a href="admin.php?tab=staff">👥 Wasimamizi na Watumishi</a></li>
                <?php endif; ?>
                <li class="<?php echo ($tab == 'meetings') ? 'active' : ''; ?>"><a href="admin.php?tab=meetings">📅 Vikao & Maelezo</a></li>
                <li class="<?php echo ($tab == 'profile') ? 'active' : ''; ?>"><a href="admin.php?tab=profile">⚙️ Badili Picha ya Wasifu</a></li>
            </ul>
        </div>
        <div class="logout-btn">
            <a href="admin.php?logout=true">&#128682; Toka Kwenye Mfumo (Home)</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h1>
                <?php 
                if ($is_driver_role) echo "Dashibodi ya Dereva: Ratiba & Kufunga Hesabu";
                elseif ($is_accountant_role) echo "Sehemu ya Kufanya Mahesabu na Kuwasilisha Miamala";
                elseif ($user_role == 'advisor') echo "Dashibodi ya Mshauri wa Kampuni";
                elseif ($is_md_or_manager) echo "Dashibodi ya Usimamizi (View-Only Mode)";
                else echo "Dashibodi Kuu ya CEO & Udhibiti wa Mizigo Mchanganyiko"; 
                ?>
            </h1>
            <div class="ceo-profile-box">
                <img src="<?php echo $_SESSION['user_photo']; ?>" class="ceo-avatar">
                <div class="ceo-info">
                    <h4><?php echo $_SESSION['user_name']; ?></h4>
                    <span><?php echo $_SESSION['user_title']; ?></span>
                </div>
            </div>
        </div>

        <?php if(!empty($msg)) echo "<div class='alert'>$msg</div>"; ?>

        <!-- KICHUPO: MPANGILIO WA TOVUTI (VIDEO YA HOME PAGE) -->
        <?php if ($tab == 'site_settings' && $is_ceo): ?>
            <div class="panel">
                <h2>🎬 Badilisha Video Inayoonekana Home Page (About Us)</h2>
                <div style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 10px; font-size:14px;">Video iliyopo sasa hivi:</h4>
                    <video controls style="width: 100%; max-width: 500px; border-radius: 8px; border: 3px solid #1b2a47;">
                        <source src="<?php echo htmlspecialchars($site_settings['home_video'] ?? 'video.mp4'); ?>" type="video/mp4">
                    </video>
                </div>
                <form method="POST" enctype="multipart/form-data" style="background:#f8f9fa; padding:20px; border-radius:8px;">
                    <label>Chagua Video Mpya (MP4):</label>
                    <input type="file" name="new_home_video" accept="video/mp4" required>
                    <button type="submit" name="update_home_video" class="btn-submit" style="background:#27ae60;">Pakia na Hifadhi Video</button>
                </form>
            </div>

        <!-- KICHUPO: ORODHA YA WALIOJISAJILI (NA UWEZO WA KU-EDIT) -->
        <?php elseif ($tab == 'manage_users' && $is_ceo): ?>
            <div class="panel" style="border-top: 4px solid #2980b9;">
                <h2>➕ Sajili Mtumishi Mpya Haraka</h2>
                <form method="POST" enctype="multipart/form-data" style="background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:20px;">
                    <div class="form-grid">
                        <div><label>Jina la Kwanza</label><input type="text" name="fname" required></div>
                        <div><label>Jina la Mwisho</label><input type="text" name="lname" required></div>
                        <div><label>Barua Pepe (Email)</label><input type="email" name="email" required></div>
                        <div>
                            <label>Cheo / Role</label>
                            <select name="role" required>
                                <option value="admin">Chief Executive Officer (CEO)</option>
                                <option value="manager">Manager</option>
                                <option value="md">Managing Director (MD)</option>
                                <option value="accountant">Mhasibu (Accountant)</option>
                                <option value="assistant_accountant">Mhasibu Msaidizi (Assistant Accountant)</option>
                                <option value="advisor">Mshauri wa Kampuni</option>
                                <option value="driver">Afisa Masoko na Dereva Mkuu</option>
                                <option value="assistant_driver">Dereva Msaidizi (Assistant Driver)</option>
                            </select>
                        </div>
                    </div>
                    <label>Maelezo (Bio)</label>
                    <textarea name="bio" rows="2" required></textarea>
                    <div class="form-grid">
                        <div><label>Nenosiri</label><input type="password" name="password" required></div>
                        <div><label>Picha ya Mtumishi</label><input type="file" name="u_photo" accept="image/*"></div>
                    </div>
                    <button type="submit" name="ceo_add_new_user" class="btn-submit" style="background:#2980b9;">Sajili Mtumishi</button>
                </form>
            </div>
            
            <div class="panel">
                <h2>Orodha ya Watumiaji Waliojisajili (Unaweza Kuhariri au Kufuta)</h2>
                <table>
                    <thead><tr><th>Picha</th><th>Jina Kamili</th><th>Email</th><th>Cheo</th><th>Vitendo / Hariri</th></tr></thead>
                    <tbody>
                        <?php foreach ($users_list as $usr): ?>
                        <tr>
                            <td><img src="<?php echo !empty($usr['photo']) ? htmlspecialchars($usr['photo']) : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'; ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"></td>
                            <td><?php echo htmlspecialchars(($usr['first_name'] ?? '') . ' ' . ($usr['last_name'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($usr['email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($usr['title'] ?? ''); ?></td>
                            <td>
                                <?php if (($usr['id'] ?? 0) !== 1): ?>
                                    <div style="display:flex; gap:5px; align-items:center;">
                                        <button type="button" class="btn-edit" onclick="document.getElementById('edit_form_<?php echo $usr['id']; ?>').style.display='block';">Hariri (Edit)</button>
                                        <a href="admin.php?tab=manage_users&delete_user_id=<?php echo $usr['id'] ?? 0; ?>" class="btn-danger" onclick="return confirm('Una uhakika unataka kumfuta mtumiaji huyu?');">Futa</a>
                                    </div>
                                    <div id="edit_form_<?php echo $usr['id']; ?>" style="display:none; background:#f8f9fa; padding:15px; border-radius:6px; margin-top:10px; border:1px solid #ddd;">
                                        <form method="POST">
                                            <input type="hidden" name="user_id" value="<?php echo $usr['id']; ?>">
                                            <label style="font-size:11px;">Jina la Kwanza:</label>
                                            <input type="text" name="edit_fname" value="<?php echo htmlspecialchars($usr['first_name'] ?? ''); ?>" style="padding:6px; font-size:12px;" required>
                                            <label style="font-size:11px;">Jina la Mwisho:</label>
                                            <input type="text" name="edit_lname" value="<?php echo htmlspecialchars($usr['last_name'] ?? ''); ?>" style="padding:6px; font-size:12px;" required>
                                            <label style="font-size:11px;">Email:</label>
                                            <input type="email" name="edit_email" value="<?php echo htmlspecialchars($usr['email'] ?? ''); ?>" style="padding:6px; font-size:12px;" required>
                                            <label style="font-size:11px;">Cheo / Role:</label>
                                            <select name="edit_role" style="padding:6px; font-size:12px;" required>
                                                <option value="admin" <?php if(($usr['role']??'')=='admin') echo 'selected'; ?>>Chief Executive Officer (CEO)</option>
                                                <option value="manager" <?php if(($usr['role']??'')=='manager') echo 'selected'; ?>>Manager</option>
                                                <option value="md" <?php if(($usr['role']??'')=='md') echo 'selected'; ?>>Managing Director (MD)</option>
                                                <option value="accountant" <?php if(($usr['role']??'')=='accountant') echo 'selected'; ?>>Mhasibu (Accountant)</option>
                                                <option value="assistant_accountant" <?php if(($usr['role']??'')=='assistant_accountant') echo 'selected'; ?>>Mhasibu Msaidizi</option>
                                                <option value="advisor" <?php if(($usr['role']??'')=='advisor') echo 'selected'; ?>>Mshauri (Advisor)</option>
                                                <option value="driver" <?php if(($usr['role']??'')=='driver') echo 'selected'; ?>>Afisa Masoko na Dereva Mkuu</option>
                                                <option value="assistant_driver" <?php if(($usr['role']??'')=='assistant_driver') echo 'selected'; ?>>Dereva Msaidizi</option>
                                            </select>
                                            <div style="margin-top:10px;">
                                                <button type="submit" name="ceo_update_user" style="background:#27ae60; color:#fff; border:none; padding:6px 12px; border-radius:3px; cursor:pointer; font-size:11px; font-weight:bold;">Hifadhi Mabadiliko</button>
                                                <button type="button" onclick="document.getElementById('edit_form_<?php echo $usr['id']; ?>').style.display='none';" style="background:#7f8c8d; color:#fff; border:none; padding:6px 12px; border-radius:3px; cursor:pointer; font-size:11px;">Funga</button>
                                            </div>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#27ae60; font-weight:bold;">CEO Mkuu</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <!-- KICHUPO: WASIMAMIZI NA WATUMISHI -->
        <?php elseif ($tab == 'staff'): ?>
            <div class="panel">
                <h2>Orodha ya Watumishi na Wasimamizi (Inajumuisha CEO)</h2>
                <table>
                    <thead><tr><th>Picha</th><th>Jina Kamili</th><th>Cheo</th><th>Bio & Mawasiliano</th><?php if ($is_ceo): ?><th>Badili</th><?php endif; ?></tr></thead>
                    <tbody>
                        <?php foreach ($staff_list as $st): ?>
                        <tr>
                            <td><img src="<?php echo !empty($st['photo']) ? htmlspecialchars($st['photo']) : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'; ?>" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;"></td>
                            <td><strong><?php echo htmlspecialchars($st['full_name'] ?? ''); ?></strong></td>
                            <td><?php echo htmlspecialchars($st['role_title'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($st['bio'] ?? ''); ?></td>
                            <?php if ($is_ceo): ?>
                            <td>
                                <form method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:4px; background:#f4f6f9; padding:8px; border-radius:6px;">
                                    <input type="hidden" name="staff_id" value="<?php echo $st['id'] ?? ''; ?>">
                                    <input type="text" name="new_role_title" value="<?php echo htmlspecialchars($st['role_title'] ?? ''); ?>" style="padding:4px; font-size:11px;" required>
                                    <textarea name="new_bio" style="padding:4px; font-size:11px; height:40px;"><?php echo htmlspecialchars($st['bio'] ?? ''); ?></textarea>
                                    <input type="file" name="new_staff_photo" accept="image/*" style="font-size:10px;">
                                    <button type="submit" name="update_staff_role" class="btn-edit" style="border:none; padding:4px;">Hifadhi</button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <!-- VICHUPO VINGINE -->
        <?php elseif ($tab == 'profile'): ?>
            <div class="panel">
                <h2>Badilisha Picha Yako ya Wasifu</h2>
                <form method="POST" enctype="multipart/form-data">
                    <img src="<?php echo $_SESSION['user_photo']; ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #1b2a47;">
                    <label>Pakia Picha Mpya:</label>
                    <input type="file" name="my_new_photo" accept="image/*" required>
                    <button type="submit" name="update_my_photo" class="btn-submit">Sasisha Picha</button>
                </form>
            </div>

        <?php elseif ($tab == 'meetings'): ?>
            <div class="panel">
                <h2>Orodha ya Vikao vya Kampuni</h2>
                <?php if ($user_role === 'advisor'): ?>
                    <form method="POST" style="margin-bottom: 25px; background: #f9f9f9; padding: 15px; border-radius: 6px;">
                        <input type="text" name="m_title" placeholder="Kichwa cha Kikao" required>
                        <input type="text" name="m_subject" placeholder="Mada Kuu (Subject)" required>
                        <textarea name="m_desc" placeholder="Maelezo..." required></textarea>
                        <button type="submit" name="create_meeting" class="btn-submit">Chapisha Kikao</button>
                    </form>
                <?php endif; ?>
                <?php foreach ($meetings_list as $m): ?>
                    <div class="meeting-card">
                        <h3><?php echo htmlspecialchars($m['title'] ?? ''); ?> (<?php echo htmlspecialchars($m['date'] ?? ''); ?>)</h3>
                        <p><strong>Mada:</strong> <?php echo htmlspecialchars($m['subject'] ?? ''); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($m['description'] ?? '')); ?></p>
                        <small>Kimeandaliwa na: <strong><?php echo htmlspecialchars($m['advisor_name'] ?? ''); ?></strong></small>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($tab == 'dashboard'): ?>
            <?php if ($is_driver_role): ?>
                <!-- RATIBA NA ROUTE KWA DEREVA / DEREVA MSAIDIZI -->
                <div class="panel" style="border-top: 4px solid #2980b9; margin-bottom: 20px;">
                    <h2 style="color:#1b2a47;">🗺️ Route na Ratiba za Safari Ulizopangiwa na CEO</h2>
                    <p style="font-size:13px; color:#555; margin-bottom:15px;">Fuata route hizi za kampuni. Kama unaendelea na route nyingine kwa sasa, tafadhali toa maoni hapa chini ili uongozi ujue.</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Tarehe</th>
                                <th>Njia / Safari (Route)</th>
                                <th>Mizigo Mchanganyiko</th>
                                <th>Gari / Maelezo</th>
                                <th>Hali & Maoni ya Dereva</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($trips_list)): ?>
                                <tr><td colspan="5" style="text-align:center;">Hakuna route mpya zilizopangwa na uongozi kwa sasa.</td></tr>
                            <?php else: ?>
                                <?php foreach ($trips_list as $tr): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tr['date'] ?? ''); ?></td>
                                    <td><strong><?php echo htmlspecialchars($tr['route'] ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars($tr['cargo'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($tr['driver'] ?? ''); ?></td>
                                    <td>
                                        <span class="badge-approved"><?php echo htmlspecialchars($tr['status'] ?? 'Active'); ?></span>
                                        <?php if (!empty($tr['driver_comment'])): ?>
                                            <div style="font-size:12px; background:#e8f8f5; color:#1e8449; padding:5px; margin-top:5px; border-radius:4px;">
                                                <strong>Maoni yako:</strong> <?php echo htmlspecialchars($tr['driver_comment']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <form method="POST" style="margin-top:8px; display:flex; gap:5px;">
                                            <input type="hidden" name="trip_id" value="<?php echo $tr['id'] ?? ''; ?>">
                                            <input type="text" name="driver_comment" placeholder="Andika maoni (Mf. Nipo route ya...)" style="padding:6px; font-size:11px;" required>
                                            <button type="submit" name="submit_driver_trip_comment" style="padding:6px 12px; font-size:11px; background:#2980b9; color:#fff; border:none; border-radius:3px; cursor:pointer;">Tuma Maoni</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel">
                    <h2>Wasilisha Hesabu na Risiti ya Safari Iliyokamilika</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="text" name="destination" placeholder="Mahali (Destination uliyotoka/upeleka)" required>
                        <input type="text" name="cargo_desc" placeholder="Aina ya Mzigo" required>
                        <input type="text" name="cargo_qty" placeholder="Kiasi (Mf. Tani 30)" required>
                        <input type="number" step="any" name="collected_amount" placeholder="Makusanyo (TZS)" required>
                        <input type="number" step="any" name="profit_amount" placeholder="Faida (TZS)" required>
                        <input type="number" step="any" name="fuel_cost" placeholder="Gharama za Mafuta (TZS)" required>
                        <input type="number" step="any" name="other_cost" placeholder="Gharama Zingine (TZS)" required>
                        <label>Picha ya Risiti:</label><input type="file" name="receipt_photo" accept="image/*" required>
                        <button type="submit" name="submit_trip_settlement" class="btn-submit">Wasilisha Hesabu</button>
                    </form>
                </div>

            <?php elseif ($is_accountant_role): ?>
                <div class="panel">
                    <h2>Eneo la Kuwasilisha Mahesabu na Miamala</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="text" name="tx_subject" placeholder="Kichwa cha Habari" required>
                        <textarea name="tx_desc" placeholder="Maelezo..." required></textarea>
                        <input type="number" step="any" name="tx_amount" placeholder="Kiasi (TZS)" required>
                        <label>Faili / Risiti:</label><input type="file" name="tx_document" accept="image/*,.pdf">
                        <button type="submit" name="accountant_submit_calc" class="btn-submit">Wasilisha kwa CEO</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="cards-modern">
                    <div class="card-modern"><h3>Mzunguko wa Fedha</h3><div class="val"><?php echo number_format($finance_data['cash_flow'] ?? 0); ?> TZS</div></div>
                    <div class="card-modern accent"><h3>Thamani ya Uwekezaji</h3><div class="val"><?php echo number_format($finance_data['investment_value'] ?? 0); ?> TZS</div></div>
                    <div class="card-modern green"><h3>Akiba (Savings)</h3><div class="val"><?php echo number_format($finance_data['savings'] ?? 0); ?> TZS</div></div>
                </div>
                
                <?php if ($is_ceo): ?>
                <div class="panel" style="border-top: 4px solid #e74c3c;">
                    <h2>🚛 Kipengele Maalum cha CEO: Ratiba & Maoni ya Dereva</h2>
                    <p style="font-size:13px; color:#555; margin-bottom:15px;">Ukiweka route hapa, dereva ataiona kwenye portal yake na kuandika maoni yake kama anaendelea na route nyingine.</p>
                    <form method="POST" style="background:#f8f9fa; padding:20px; border-radius:8px; margin-bottom:20px;">
                        <div class="form-grid">
                            <div><label>Njia (Route):</label><input type="text" name="route_name" placeholder="Mf. Namanga - Arusha - Nairobi" required></div>
                            <div><label>Dereva/Gari:</label><input type="text" name="driver_assigned" placeholder="Mf. Scania Truck / Dereva Mkuu" required></div>
                            <div><label>Mizigo Mchanganyiko:</label><input type="text" name="cargo_details" placeholder="Mf. Bidhaa za jamii na kilimo" required></div>
                            <div><label>Makadirio Mapato (TZS):</label><input type="number" name="est_revenue" placeholder="0" required></div>
                        </div>
                        <button type="submit" name="ceo_add_mixed_trip" class="btn-submit">Chapisha Route kwa Dereva</button>
                    </form>
                    <table>
                        <thead><tr><th>Tarehe</th><th>Njia / Safari</th><th>Mizigo</th><th>Dereva</th><th>Mapato</th><th>Hali & Maoni ya Dereva</th></tr></thead>
                        <tbody>
                            <?php foreach ($trips_list as $tr): ?>
                            <tr>
                                <td><?php echo $tr['date'] ?? ''; ?></td>
                                <td><strong><?php echo $tr['route'] ?? ''; ?></strong></td>
                                <td><?php echo $tr['cargo'] ?? ''; ?></td>
                                <td><?php echo $tr['driver'] ?? ''; ?></td>
                                <td><?php echo number_format($tr['revenue'] ?? 0); ?> TZS</td>
                                <td>
                                    <span class="badge-approved"><?php echo $tr['status'] ?? ''; ?></span>
                                    <?php if (!empty($tr['driver_comment'])): ?>
                                        <div style="font-size:11px; background:#fff8e1; color:#795548; padding:5px; margin-top:5px; border-radius:4px;">
                                            <strong>Maoni ya Dereva:</strong> <?php echo htmlspecialchars($tr['driver_comment']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <div class="panel">
                    <h2>Miamala na Hesabu Zilizowasilishwa</h2>
                    <table>
                        <thead><tr><th>Tarehe</th><th>Mtumishi</th><th>Maelezo / Safari</th><th>Kiasi</th><th>Hali</th><th>Risiti/Hati</th><th>Maoni ya CEO</th></tr></thead>
                        <tbody>
                            <?php foreach ($finance_data['transactions'] as $tx): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tx['date'] ?? ''); ?></td>
                                <td><strong><?php echo htmlspecialchars($tx['driver_name'] ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($tx['cargo_desc'] ?? $tx['subject'] ?? 'Maelezo Hakuna'); ?></td>
                                <td><?php echo number_format($tx['amount'] ?? 0); ?> TZS</td>
                                <td><?php echo (($tx['accountant_status'] ?? 'pending') == 'approved') ? '<span class="badge-approved">Imepitishwa</span>' : '<span class="badge-pending">Inasubiri</span>'; ?></td>
                                <td><?php if (!empty($tx['receipt_photo'])): ?><a href="<?php echo htmlspecialchars($tx['receipt_photo']); ?>" target="_blank" class="receipt-link">Tazama Faili</a><?php endif; ?></td>
                                <td>
                                    <?php if (!empty($tx['ceo_comment'])): ?><div style="font-size:12px; background:#fff8e1; padding:5px;"><strong>CEO:</strong> <?php echo htmlspecialchars($tx['ceo_comment']); ?></div><?php endif; ?>
                                    <?php if ($is_ceo): ?>
                                        <form method="POST" style="margin-top:5px; display:flex; gap:5px;">
                                            <input type="hidden" name="tx_id" value="<?php echo htmlspecialchars($tx['id'] ?? ''); ?>">
                                            <input type="text" name="ceo_comment" placeholder="Weka maoni..." style="padding:4px; font-size:11px;" required>
                                            <button type="submit" name="save_ceo_comment" style="padding:4px; font-size:11px; background:#2980b9; color:#fff; border:none; border-radius:3px; cursor:pointer;">Tuma</button>
                                        </form>
                                        <?php if (($tx['accountant_status'] ?? 'pending') !== 'approved'): ?>
                                            <form method="POST" style="margin-top:5px;">
                                                <input type="hidden" name="tx_id" value="<?php echo htmlspecialchars($tx['id'] ?? ''); ?>">
                                                <button type="submit" name="approve_accountant_tx" style="width:100%; padding:5px; font-size:11px; background:#27ae60; color:#fff; border:none; border-radius:3px; cursor:pointer;">Pitisha Hati</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
