## TEA APPLICATION
#### Randomly generate a user to make the brews in your workplace
##### An email is sent to the brew maker with everyones order

###### How to use:
* clone the repo to your computer
* import tea.sql in to your PHPMYADMIN
* go to includes/db_connection.php file and change the constant values to your server settings
* go to app/call_round.php, line ~73 change the email of the 'FROM' recipient, to an email from your server/domain
* the default email is 'admin@admin.com', and the default password is 'password' to access the application initially as administrator
* once you are logged in you can make yourself as an administrator and delete the default. There is also functionality to change your password
* under the users section, you can add people to the tea list with their personal information and drinks preference.
* press the 'call round' btn on the index page to randomly select a user to make the brews, this will then email the recipient with a list of drinks to make

###### Functionalities:
* Add user to the tea rota
* view each users information, including how many times that user has had to make the brews
* Add administrator
* Delete administrator
* Change administrator password
* Randomly select a user to make the brews
* Email the randomly selected user with a list of all users entered in the tea rota drink preference