#Installation
1 - Install Laravel Homestead -> installs all needed dependencies for the proyect
    https://laravel.com/docs/5.0/homestead
    https://styde.net/instalacion-y-configuracion-de-laravel-homestead/

Homestead.yaml and hosts.txt file is on configurationFiles.zip

2 - "vagrant up" to start the virtual machine

3 - "vagrant ssh" to change to vagrant user and copy the app code on the route expecified in Homestead.yaml "/home/vagrant/Proyects"

4 - "exit" to return to your user and "vagrant reload --provision" to update vagrant with changes

5 - "vagrant ssh" to change to vagrant user and inside the proyect folder "coin-chatbot" run "php artisan migrate --seed" to run all migrations and seeders

6 - Change the API_CONVERT_CURRENCY_KEY on the .env file for the key from https://www.amdoren.com/developer/home/

7 - The app must be running at https://coin-chatbot.test/

