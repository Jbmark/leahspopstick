
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Leah's Popstick System</title>

    <link rel="stylesheet" href="/public/css/style.css?v=mobilefix4">


<style>
    .mobile-menu-bar {
    display: none;
}

@media screen and (max-width: 700px) {
    .mobile-menu-bar {
        position: relative !important;
        z-index: 10000 !important;
        isolation: isolate;
        pointer-events: auto !important;
    }

    .mobile-menu-toggle {
        position: relative !important;
        z-index: 10001 !important;
        pointer-events: auto !important;
        touch-action: manipulation;
    }
}

    @media screen and (max-width: 700px) {
        .mobile-menu-bar {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 56px;
            padding: 8px 16px;
            background: #fff;
            border-bottom: 1px solid #E5E9F0;
            position: relative;
            z-index: 1001;
            isolation: isolate;
    		z-index: 9999;
            
        }

        .mobile-menu-toggle {
            width: 42px;
            height: 42px;
            border: 1px solid #E5E9F0;
            border-radius: 8px;
            background: #fff;
            color: #1B5E20;
            font-size: 25px;
            cursor: pointer;
            position: relative;
    		z-index: 10000;
    		pointer-events: auto;
        }

        .container {
            display: flex;
            flex-direction: row !important;
            min-height: calc(100vh - 56px);
        }

        #adminSidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            z-index: 1003 !important;
            width: 280px !important;
            min-width: 0 !important;
            max-width: 85vw !important;
            height: 100vh !important;
            min-height: 100vh !important;
            overflow-y: auto !important;
            background: #fff !important;
            border-right: 1px solid #E5E9F0 !important;
            border-bottom: none !important;
            transform: translateX(-100%) !important;
            visibility: hidden !important;
            transition: transform .25s ease, visibility .25s ease !important;
        }

        #adminSidebar.is-open {
            transform: translateX(0) !important;
            visibility: visible !important;
        }

        #sidebarOverlay {
            display: none;
        }

        #sidebarOverlay.is-open {
            display: block !important;
            position: fixed !important;
            inset: 0 !important;
            z-index: 1002 !important;
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            border: none !important;
            background: rgba(0, 0, 0, .35) !important;
        }

        .content {
            flex: 1;
            width: 100%;
            min-width: 0;
            padding: 20px 16px;
        }
    }
    
    .visit-shop-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-left: auto;
    padding: 9px 13px;
    border: 1px solid #1b5e20;;
    border-radius: 8px;
    background: #1b5e20;;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
}

.visit-shop-btn:hover {
    background: #1B6847;
}

.visit-shop-btn span {
    color: #fff !important;
    -webkit-text-fill-color: #fff !important;
}

/* Smaller button on very narrow mobile screens */
@media screen and (max-width: 380px) {
    .mobile-menu-bar {
        gap: 6px;
        padding-left: 8px;
        padding-right: 8px;
    }

    .mobile-brand {
        font-size: 12px;
    }

    .visit-shop-btn {
        padding: 6px 7px;
        font-size: 11px;
        gap: 3px;
    }
}

@media screen and (max-width: 700px) {
    .desktop-shop-bar {
        display: none;
    }
}

@media screen and (max-width: 700px) {
    .content .dashboard-heading > .dashboard-visit-shop {
        display: none !important;
    }
}

/* Force Visit Shop text and icon to remain white */
a.visit-shop-btn,
a.visit-shop-btn:link,
a.visit-shop-btn:visited,
a.visit-shop-btn:hover,
a.visit-shop-btn:active,
a.visit-shop-btn span {
    color: #FFFFFF !important;
    -webkit-text-fill-color: #FFFFFF !important;
}


.dashboard-heading .dashboard-visit-shop {
    align-self: center;
    transform: translateY(-35px);
}
</style>

</head>

<body>

<?php if (!empty($_SESSION['user'])) { ?>
    <div class="mobile-menu-bar">
        <button
            type="button"
            id="mobileMenuToggle"
            class="mobile-menu-toggle"
            aria-label="Open navigation menu"
            aria-controls="adminSidebar"
            aria-expanded="false"
        >
            ☰
        </button>

                <span class="mobile-brand">Leah's Popstick</span>

        <a
            href="/public/shop/index.php"
            class="visit-shop-btn"
            target="_blank"
            rel="noopener noreferrer"
        >
            <span aria-hidden="true">🛍</span>
            <span>Visit Shop</span>
        </a>
    </div>
<?php } ?>

<?php if (!empty($_SESSION['user'])) { ?>
    <div class="desktop-shop-bar">
        <span class="desktop-header-spacer"></span>
    </div>
<?php } ?>