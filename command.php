<?php

use Illuminate\Support\Facades\Route;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/create/{project}', function ($project) {

    // Load RSA private key
    $key = PublicKeyLoader::load(file_get_contents('/var/www/html/.keys/key'));
    // Login to ssh
    $ssh = new SSH2('create.g-web.agency');
    if (!$ssh->login('root', $key)) {
        exit('Login Failed');
    }

    // Create project
    $ssh->exec('cd /var/www/html && mkdir ' . $project . '.g-web.agency');
    // Create index file inside
    $ssh->exec('cd /var/www/html/' . $project . '.g-web.agency && echo "<?php echo \'Hello World!\'; ?>" > index.php');
    // Git status
    echo $ssh->exec('cd /var/www/html/ && git status && git add .');
    echo $ssh->exec('cd /var/www/html/ && git commit -m "from-g-web-server"');
    echo $ssh->exec('cd /var/www/html/ && git push origin');

    // Create virtualhost
    $ssh->exec('cd /var/www/html/ && virtualhost create ' . $project . '.g-web.agency /var/www/html/' . $project . '.g-web.agency');

    // 
    // certbot --apache -d example.domain.com and choose 2
    // $ssh->exec('cd /var/www/html/ && certbot --apache -d ' . $project . '.g-web.agency --redirect');
});

// Check if subdomain is available
Route::get('/check-domain/{project}', function ($project) {
    // Load RSA private key
    $key = PublicKeyLoader::load(file_get_contents('/var/www/html/.keys/key'));
    // Login to ssh
    $ssh = new SSH2('create.g-web.agency');
    if (!$ssh->login('root', $key)) {
        exit('Login Failed');
    }

    // Check if folder exists
    echo $ssh->exec('cd /var/www/html && if [ -d "' . $project . '.g-web.agency" ]; then echo "true"; else echo "false"; fi');
});

// Build the website
Route::post('/build', function (Request $request) {
    // Git clone the repo
    // Load RSA private key
    $key = PublicKeyLoader::load(file_get_contents('/var/www/html/.keys/key'));
    // Login to ssh
    $ssh = new SSH2('create.g-web.agency');
    if (!$ssh->login('root', $key)) {
        exit('Login Failed');
    }


    // Clone repo to folder with project name and domain name and show real time progress
    echo $ssh->exec('cd /var/www/html && git clone ' . $request->repo . ' ' . $request->domain . '.g-web.agency');
    // Check if repo is cloned
    $isCloned = $ssh->exec('cd /var/www/html && if [ -d "' . $request->domain . '.g-web.agency" ]; then echo "========> <span class="text-pink-400">CLONNED SUCCESSFULLY!</span>"; else echo "false"; fi');
    echo $isCloned;

    if ($isCloned) {
        echo '<br> Now lets install the dependencies...';
        echo $ssh->exec('cd /var/www/html/' . $request->domain . '.g-web.agency && npm install');
        echo $ssh->exec('cd /var/www/html/' . $request->domain . '.g-web.agency && if [ -d "node_modules" ]; then echo "<br> ========> <span class="text-pink-400">NPM INSTALLED SUCCESSFULLY!</span>"; else echo "false"; fi');
    }

    // // Npm install and build
    // echo 'npm install start';
    // echo $ssh->exec('cd /var/www/html/' . $request->domain . '.g-web.agency && npm install');
    // echo 'npm install done';
    // // Npm build
    // echo 'npm build start';
    // echo $ssh->exec('cd /var/www/html/' . $request->domain . '.g-web.agency && npm run build');
    // echo 'npm build done';
    // composer install
    // echo $ssh->exec('cd /var/www/html/' . $request->domain .
    //     '.g-web.agency && composer update && composer install && php bin/console doc:mig:mig', function ($output) {
    //     echo '<br/>' . $output;
    // });
    // // Check if success
    // echo $ssh->exec('cd /var/www/html && if [ -d "' . $request->domain . '.g-web.agency" ]; then echo "<br/>The repository is <span class="text-pink-400">successfully</span> cloned to the server :)"; else echo "Could not be cloned to the server"; fi');
    // echo '<br/>Creating virtualhost...';
    // // Create virtualhost
    // $ssh->exec('cd /var/www/html/ && virtualhost create ' . $request->domain . '.g-web.agency /var/www/html/' . $request->domain . '.g-web.agency/dist/');
    // echo '<br/>Virtualhost created!';
    // // Restart apache
    // echo '<br/>Restarting apache...';
    // $ssh->exec('cd /var/www/html/ && service apache2 restart');
    // echo '<br/>Waiting for the domain to be available...';
    // // Install SSL
    // echo '<br/>Installing SSL...';
    // echo $ssh->exec('cd /var/www/html/ && certbot --apache -d ' . $request->domain . '.g-web.agency --redirect');
    // echo '<br/>SSL installed!';
    // // Wait for the domain to be available
    // sleep(5);
    // echo '<br/>Domain is available --> <a class="text-pink-400 animate-pulse underline" href="https://' . $request->domain . '.g-web.agency" target="_blank">' . $request->domain . '.g-web.agency</a>';
});
