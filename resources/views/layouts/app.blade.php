
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'WorkFlow360')</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            color: #333;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background-color: #1f2937;
            color: white;
            padding: 20px;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 35px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            display: block;
            color: #d1d5db;
            text-decoration: none;
            padding: 12px;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background-color: #374151;
            color: white;
        }

        /* Partie principale */
        .main {
            flex: 1;
        }

        /* Navbar */
        .navbar {
            height: 70px;
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            border-bottom: 1px solid #ddd;
        }

        .navbar h2 {
            font-size: 20px;
        }

        .user {
            color: #555;
        }

        /* Contenu */
        .content {
            padding: 30px;
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="logo">
            WorkFlow360
        </div>

        <ul>
            <li>
                <a href="#"> Accueil</a>
            </li>

            <li>
                <a href="#"> Mes tâches</a>
            </li>

            <li>
                <a href="#"> Projets</a>
            </li>

            <li>
                <a href="#"> Mes demandes</a>
            </li>

            <li>
                <a href="#"> Équipe</a>
            </li>

            <li>
                <a href="#"> Profil</a>
            </li>
        </ul>

    </aside>


    <!-- Contenu principal -->
    <main class="main">

        <!-- Navbar -->
        <nav class="navbar">

            <h2>@yield('page-title', 'Tableau de bord')</h2>

            <div class="user">
                👤 Utilisateur
            </div>

        </nav>


        <!-- Contenu de la page -->
        <section class="content">

            @yield('content')

        </section>

    </main>

</div>

</body>
</html>
```
