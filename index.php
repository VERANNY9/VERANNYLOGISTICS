<?php
$success_msg = "";
$whatsapp_link = "";

// Kusoma viongozi/watumishi kutoka kwenye leaders.json
$leaders_file = 'leaders.json';
$leaders = [];
if (file_exists($leaders_file)) {
    $leaders = json_decode(file_get_contents($leaders_file), true);
} else {
    // Default yenye watumishi na viongozi wote
    $leaders = [
        [
            "name" => "NELIUS STEVEN", 
            "role" => "CEO", 
            "bio" => "Kiongozi Mkuu mwenye maono ya kuendesha kampuni na kusimamia mikakati yote ya kibiashara.", 
            "image" => "uploads/nelius.jpeg"
        ],
        [
            "name" => "ALISTIDES STEVEN", 
            "role" => "Managing Director", 
            "bio" => "Anasimamia shughuli za kila siku za uendeshaji na utekelezaji wa malengo ya kampuni.", 
            "image" => "uploads/alistides.jpeg"
        ],
        [
            "name" => "STELLA JONATHANI", 
            "role" => "Director", 
            "bio" => "Anatoa miongozo ya kimkakati na kusimamia maendeleo ya miradi ya kampuni.", 
            "image" => "uploads/stella.jpg"
        ],
        [
            "name" => "NASSORO JUMA", 
            "role" => "Afisa Masoko & Dereva Mkuu", 
            "bio" => "Anaratibu masoko, upatikanaji wa wateja, na uendeshaji wa usafirishaji wa kampuni.", 
            "image" => "uploads/nassoro.jpg"
        ],
        [
            "name" => "LIVINUS MWIJAGE", 
            "role" => "Muhasibu", 
            "bio" => "Anasimamia masuala yote ya fedha, mapato, matumizi, na hesabu za kampuni.", 
            "image" => "uploads/livinus.jpg"
        ],
        [
            "name" => "ELIAH GUGAH", 
            "role" => "Mshauri wa Kampuni", 
            "bio" => "Anatoa ushauri wa kitaalamu wa kisheria, uwekezaji, na mikakati ya kukuza biashara.", 
            "image" => "uploads/eliag.jpg"
        ]
    ];
    file_put_contents($leaders_file, json_encode($leaders, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_order'])) {
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $service = htmlspecialchars($_POST['service']);
    $product = htmlspecialchars($_POST['product']);
    $details = htmlspecialchars($_POST['details']);
    
    $order_data = [
        'date' => date('Y-m-d H:i:s'),
        'name' => $name,
        'phone' => $phone,
        'service' => $service,
        'product' => $product,
        'details' => $details
    ];
    $file = 'orders.json';
    $current = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $current[] = $order_data;
    file_put_contents($file, json_encode($current, JSON_PRETTY_PRINT));
    
    $wa_text = "Habari Veranny Logistics,%0A%0ANina oda/ujumbe mpya kutoka kwa:%0A*Jina:* $name%0A*Simu:* $phone%0A*Huduma:* $service%0A*Bidhaa/Mzigo:* $product%0A*Maelezo:* $details";
    $whatsapp_link = "https://wa.me/255753431912?text=" . $wa_text;
    
    $success_msg = "Oda yako imepokewa kikamilifu! Bonyeza kitufe hapa chini kutuma ujumbe huu moja kwa moja WhatsApp.";
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veranny Logistics | Heavy Commercial & Produce Transport</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-blue: #1c2653; 
            --brand-red: #d32027;  
            --bg-light: #f4f7f6;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-light);
            scroll-behavior: smooth;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Montserrat', sans-serif;
        }

        .public-nav {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .public-nav .nav-link {
            color: var(--brand-blue);
            font-weight: 600;
            margin: 0 3px;
            transition: 0.3s;
            font-size: 14px;
        }
        .public-nav .nav-link:hover, .public-nav .nav-link.active {
            color: var(--brand-red);
        }
        .logo-img {
            height: 45px;
            object-fit: contain;
        }

        .hero-slide {
            padding: 140px 0;
            color: white;
            text-align: center;
        }

        .section-padding {
            padding: 80px 0;
        }

        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .card-custom:hover {
            transform: translateY(-5px);
        }

        .chat-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .chat-box {
            height: 250px;
            overflow-y: auto;
            background: #f9fbfb;
        }
    </style>
</head>
<body>

    <!-- Navbar ikiwa na Logo ya Kampuni -->
    <nav class="navbar navbar-expand-lg public-nav sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <img src="logo.jpeg" alt="Veranny Logistics" class="logo-img" onerror="this.src='https://placehold.co/150x45?text=Veranny+Logistics'">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About Us & Team</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Mazao & Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">Contact Us & AI</a></li>
                </ul>
                <a href="admin.php" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"><i class="fas fa-lock me-1"></i> CEO Login</a>
            </div>
        </div>
    </nav>

    <!-- 1. HOME SECTION (Carousel Slides) -->
    <section id="home">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <div class="carousel-item active hero-slide" style="background: linear-gradient(rgba(28, 38, 83, 0.85), rgba(28, 38, 83, 0.85)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;">
                    <div class="container">
                        <h1 class="display-4 fw-bold mb-3">Heavy Commercial & Produce Transport</h1>
                        <p class="lead mb-4">Usafirishaji wa mizigo mchanganyiko na mazao ya kilimo kwa usalama mkubwa na kwa wakati sahihi.</p>
                        <a href="#contact" class="btn btn-danger btn-lg px-4 fw-bold">Weka Oda Sasa</a>
                    </div>
                </div>
                <div class="carousel-item hero-slide" style="background: linear-gradient(rgba(28, 38, 83, 0.85), rgba(28, 38, 83, 0.85)), url('https://images.unsplash.com/photo-1519003722824-194d4455a60c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;">
                    <div class="container">
                        <h1 class="display-4 fw-bold mb-3">Soko la Mazao & Usafiri wa Uhakika</h1>
                        <p class="lead mb-4">Tunakuunganisha na biashara za mazao na kusafirisha shehena yako popote ilipo.</p>
                        <a href="#services" class="btn btn-outline-light btn-lg px-4 fw-bold">Angalia Huduma</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. ABOUT US SECTION -->
    <section id="about" class="section-padding bg-white">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="ratio ratio-16x9 shadow rounded overflow-hidden">
                        <video controls class="w-100 h-100" style="object-fit: cover;">
                            <source src="video.mp4" type="video/mp4">
                            Browser yako haiauni video.
                        </video>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-danger fw-bold text-uppercase">About Us</h6>
                    <h2 class="fw-bold mb-3" style="color: var(--brand-blue);">Veranny Logistics</h2>
                    <p class="text-muted">Sisi ni kampuni inayoaminika katika kutoa huduma za usafirishaji wa mizigo mchanganyiko, shehena za kibiashara, pamoja na kusimamia biashara ya ununuzi na uuzaji wa mazao ya kilimo.</p>
                    
                    <div class="card border-0 bg-light p-4 shadow-sm border-start border-danger border-4 mt-4">
                        <h5 class="fw-bold text-dark">Dira Yetu</h5>
                        <p class="text-muted mb-0">"Kutoa suluhisho la haraka, salama na la kuaminika la usafirishaji na biashara ya mazao ili kukuza uchumi wa wateja wetu."</p>
                    </div>
                </div>
            </div>

            <!-- WATUMISHI NA VIONGOZI -->
            <div class="text-center mt-5 pt-4 mb-5">
                <h6 class="text-danger fw-bold text-uppercase">Uongozi na Watumishi</h6>
                <h2 class="fw-bold" style="color: var(--brand-blue);">Timu Yetu ya Kazi</h2>
                <p class="text-muted">Wataalamu wanaosimamia ubora na ufanisi wa huduma zetu kila siku.</p>
            </div>

            <div class="row">
                <?php foreach($leaders as $leader): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card card-custom text-center p-4 h-100 bg-light">
                            <img src="<?php echo htmlspecialchars($leader['image']); ?>" alt="<?php echo htmlspecialchars($leader['name']); ?>" class="rounded-circle mx-auto mb-3 shadow" style="width: 130px; height: 130px; object-fit: cover;" onerror="this.src='https://placehold.co/130x130?text=No+Photo'">
                            <h4 class="fw-bold mb-1 fs-5"><?php echo htmlspecialchars($leader['name']); ?></h4>
                            <p class="text-danger fw-bold mb-2 small"><?php echo htmlspecialchars($leader['role']); ?></p>
                            <p class="text-muted small"><?php echo htmlspecialchars($leader['bio']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 3. SERVICES SECTION -->
    <section id="services" class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-danger fw-bold text-uppercase">Huduma Zetu</h6>
                <h2 class="fw-bold" style="color: var(--brand-blue);">Tunachowafanyia Wateja Wetu</h2>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card card-custom p-4 text-center h-100 bg-white">
                        <i class="fas fa-truck-moving fa-3x mb-3 text-danger"></i>
                        <h4 class="fw-bold">Usafirishaji wa Mizigo</h4>
                        <p class="text-muted">Tunasafirisha mizigo ya mchanganyiko na bidhaa za kibiashara kwa usalama mkubwa.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card card-custom p-4 text-center h-100 bg-white">
                        <i class="fas fa-seedling fa-3x mb-3" style="color: var(--brand-blue);"></i>
                        <h4 class="fw-bold">Biashara ya Mazao</h4>
                        <p class="text-muted">Tunajihusisha na ununuzi na usambazaji wa mazao ya kilimo kutoka mashambani hadi sokoni.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card card-custom p-4 text-center h-100 bg-white">
                        <i class="fas fa-handshake fa-3x mb-3 text-danger"></i>
                        <h4 class="fw-bold">Ushauri wa Kibiashara</h4>
                        <p class="text-muted">Tunatoa miongozo na ushauri wa kitaalamu wa kukuza uwekezaji na usafirishaji mizigo.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. MAZAO & ORDERS SECTION -->
    <section id="contact" class="section-padding bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-4">
                        <h6 class="text-danger fw-bold text-uppercase">Mazao & Orders</h6>
                        <h2 class="fw-bold" style="color: var(--brand-blue);">Weka Oda Yako ya Usafiri au Mazao</h2>
                        <p class="text-muted">Jaza fomu hii kuweka oda yako. Itatunzwa na kukuwezesha kutuma ujumbe moja kwa moja WhatsApp.</p>
                    </div>

                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success text-center p-4 shadow-sm">
                            <h5 class="fw-bold"><i class="fas fa-check-circle me-2"></i> Umefaulu!</h5>
                            <p class="mb-3"><?php echo $success_msg; ?></p>
                            <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="btn btn-success btn-lg fw-bold">
                                <i class="fab fa-whatsapp me-2"></i> Tuma Ujumbe Huu Kwenye WhatsApp
                            </a>
                        </div>
                    <?php endif; ?>

                    <form action="#contact" method="POST" class="card card-custom p-4 p-md-5 bg-light">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jina Lako Kamili</label>
                                <input type="text" name="name" class="form-control" placeholder="Mf. Juma Ally" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Namba ya Simu</label>
                                <input type="text" name="phone" class="form-control" placeholder="Mf. 07xxxxxxxx" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Aina ya Huduma</label>
                                <select name="service" class="form-select" required>
                                    <option value="Kusafirisha Mzigo (Transport)">Kusafirisha Mzigo (Transport)</option>
                                    <option value="Kununua / Kuagiza Mazao">Kununua / Kuagiza Mazao</option>
                                    <option value="Ushauri wa Kibiashara">Ushauri wa Kibiashara</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jina la Bidhaa / Aina ya Mzigo</label>
                                <input type="text" name="product" class="form-control" placeholder="Mf. Mahindi / Tani 10" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Maelezo ya Ziada / Mahali pa Kuchukua Mzigo</label>
                            <textarea name="details" rows="4" class="form-control" placeholder="Andika maelezo kamili hapa..." required></textarea>
                        </div>
                        <button type="submit" name="submit_order" class="btn btn-danger btn-lg w-100 fw-bold">
                            <i class="fas fa-paper-plane me-2"></i> Tuma Oda Yako Sasa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. CONTACT US & AI ASSISTANT -->
    <section id="faq" class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-danger fw-bold text-uppercase">Contact Us & FAQ</h6>
                <h2 class="fw-bold" style="color: var(--brand-blue);">Mawasiliano ya Haraka & Roboti ya AI</h2>
                <p class="text-muted">Wasiliana moja kwa moja na uongozi au uliza swali lolote kwenye Roboti ya AI.</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <!-- KIZUIZI CHA MAWASILIANO YA AFISA MASOKO NA DEREVA MKUU -->
                    <div class="card border-0 shadow-sm p-4 mb-4 border-start border-success border-4 bg-white">
                        <h5 class="fw-bold text-success mb-2"><i class="fas fa-phone-alt me-2"></i> Afisa Masoko na Dereva Mkuu</h5>
                        <p class="mb-1"><strong>Jina:</strong> Nassoro Juma (Mahengo)</p>
                        <p class="mb-1"><strong>Simu ya Mkononi:</strong> +255 652 009 916</p>
                        <p class="mb-1"><strong>WhatsApp:</strong> +255 753 431 912</p>
                        <p class="mb-3"><strong>Barua Pepe:</strong> info@verannylogistics.com</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="tel:+255652009916" class="btn btn-success btn-sm fw-bold"><i class="fas fa-phone me-1"></i> Piga Simu</a>
                            <a href="https://wa.me/255753431912" target="_blank" class="btn btn-success btn-sm fw-bold"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a>
                            <a href="mailto:info@verannylogistics.com" class="btn btn-primary btn-sm fw-bold"><i class="fas fa-envelope me-1"></i> Tuma Email</a>
                        </div>
                    </div>

                    <!-- AI CHAT ASSISTANT YENYE UWEZO WA KU-TYPE SWALI LOLOTE -->
                    <div class="chat-container p-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-robot text-danger me-2"></i> Uliza Swali Lolote kwa AI Assistant</h5>
                        <div class="chat-box p-3 rounded mb-3 border" id="chatResponse">
                            <div class="mb-2"><strong>🤖 AI Assistant:</strong> Karibu Veranny Logistics Contact Desk! Andika swali lako lolote hapa chini kuhusu bei, usafirishaji, au mazao, au bonyeza maswali ya haraka.</div>
                        </div>

                        <!-- Sehemu ya Kuandika Swali La Kujitegemea (Free Type Input) -->
                        <div class="input-group mb-3">
                            <input type="text" id="chatInput" class="form-control" placeholder="Andika swali lako hapa... (Mf. Mnasafirisha kwenda mkoa gani?)" onkeypress="if(event.key === 'Enter') sendCustomAI();">
                            <button class="btn btn-danger fw-bold px-4" type="button" onclick="sendCustomAI()">Tuma</button>
                        </div>

                        <!-- Vifungo vya Maswali ya Haraka (Preset Buttons) -->
                        <p class="small text-muted mb-2">Au bonyeza maswali haya ya haraka:</p>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="askAI('Bei zenu ziko vipi?')">Bei zenu ziko vipi?</button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="askAI('Mnasafirisha mikoa gani?')">Mnasafirisha mikoa gani?</button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="askAI('Mnauza mazao gani?')">Mnauza mazao gani?</button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="askAI('Namba zenu za simu?')">Namba zenu za simu?</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p class="mb-0">&copy; 2026 Veranny Logistics. Haki zote zimehifadhiwa.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function sendCustomAI() {
            const input = document.getElementById('chatInput');
            const q = input.value.trim();
            if(!q) return;
            processAIQuery(q);
            input.value = '';
        }

        function askAI(questionText) {
            processAIQuery(questionText);
        }

        function processAIQuery(query) {
            const chatBox = document.getElementById('chatResponse');
            let qLower = query.toLowerCase();
            let aiAns = "";

            if(qLower.includes('bei') || qLower.includes('gharama')) {
                aiAns = "Bei zetu za usafirishaji na mazao zinategemea uzito wa mzigo na umbali wa safari. Tafadhali wasiliana moja kwa moja na Afisa Masoko na Dereva Mkuu wetu Nassoro Juma kupitia namba +255 652 009 916 kupata bei maalum.";
            } else if(qLower.includes('route') || qLower.includes('safari') || qLower.includes('mkoa') || qLower.includes('mnasafirisha')) {
                aiAns = "Tunasafirisha mizigo mchanganyiko na mazao ya kilimo ndani na nje ya nchi kwa malori yetu ya kuaminika ya Veranny Logistics.";
            } else if(qLower.includes('mazao') || qLower.includes('kilimo') || qLower.includes('mahindi') || qLower.includes('mpunga')) {
                aiAns = "Tunajihusisha na biashara na usambazaji wa mazao ya kilimo kama mahindi na mpunga moja kwa moja kutoka mashambani.";
            } else if(qLower.includes('namba') || qLower.includes('simu') || qLower.includes('mawasiliano') || qLower.includes('dereva') || qLower.includes('masoko')) {
                aiAns = "Mawasiliano ya haraka ya Afisa Masoko na Dereva Mkuu, Nassoro Juma (Mahengo): Simu: +255 652 009 916, WhatsApp: +255 753 431 912.";
            } else {
                aiAns = "Asante kwa swali lako ('" + query + "'). Kwa huduma za haraka au maelezo zaidi, tafadhali wasiliana na Afisa Masoko wetu Nassoro Juma kupitia +255 652 009 916 au WhatsApp +255 753 431 912.";
            }

            chatBox.innerHTML += `<div class="mb-2 text-end"><strong>Wewe:</strong> ${query}</div>`;
            chatBox.innerHTML += `<div class="mb-2 text-primary"><strong>🤖 AI Assistant:</strong> ${aiAns}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</body>
</html>