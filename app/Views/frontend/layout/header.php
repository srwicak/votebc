<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="E-Voting BEM Universitas - Platform voting digital yang aman dan transparan berbasis blockchain">
    <title><?= $title ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#4f46e5', // indigo-600
                            hover: '#4338ca', // indigo-700
                        },
                        secondary: {
                            DEFAULT: '#14b8a6', // teal-500
                            hover: '#0d9488', // teal-600
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <?php if (isset($page) && strpos($page, 'admin') !== false): ?>
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <?php endif; ?>
    
    <style type="text/tailwindcss">
        @layer utilities {
            .transition-all {
                transition-property: all;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 150ms;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div id="app" class="flex flex-col min-h-screen">