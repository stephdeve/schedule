<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Schedule</title>
    @vite(['resources/css/app.css','resources/css/theme.css'])
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Material+Icons+Round&display=swap" rel="stylesheet">
    <style>
        .login-bg {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath fill='%231565C0' fill-opacity='0.05' d='M50,0 C77.6,0 100,22.4 100,50 C100,77.6 77.6,100 50,100 C22.4,100 0,77.6 0,50 C0,22.4 22.4,0 50,0 Z M50,10 C27.9,10 10,27.9 10,50 C10,72.1 27.9,90 50,90 C72.1,90 90,72.1 90,50 C90,27.9 72.1,10 50,10 Z'/%3E%3C/svg%3E");
            background-size: 120px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center login-bg p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="mx-auto bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mb-4"></div>
                <h1 class="text-2xl font-bold text-gray-800">Schedule Administration</h1>
                <p class="text-gray-600 mt-2">Système de gestion des emplois du temps universitaires</p>
            </div>
            
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="space-y-4">
                    <div class="float-label-group">
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder=" ">
                        <label for="email">Adresse email</label>
                    </div>
                    
                    <div class="float-label-group">
                        <input type="password" id="password" name="password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder=" ">
                        <label for="password">Mot de passe</label>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700">Se souvenir de moi</span>
                    </label>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">Mot de passe oublié?</a>
                </div>
                
                <button type="submit" class="w-full mt-8 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300">
                    Se connecter
                </button>
            </form>
        </div>
        
        <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-200">
            <p class="text-gray-600 text-sm">
                © {{ date('Y') }} Schedule. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>
