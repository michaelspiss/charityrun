**CharityRun**

CharityRun is a software that was developed for the Gymnasium Raubling, which
does a charity run every four years for aid organizations in Sri Lanka and Bolivia.
The goal was to display the distance, that was run in total by the school
on a map, with the ultimate goal of reaching Sri Lanka or Bolivia. Over time,
the software's tasks grew and it has become rich in features, such as also being
responsible for calculating the overall donations per person, that was previously done by hand.

The first version of the software was used at the charity run in 2014, where it managed roughly a thousand
pupils and teachers.

For all other runs, beginning as of 2018, the software was completely rebuilt
from scratch, with real world experience from the first run.

I decided to release it as open source software under the MIT license, to allow
others to build upon this platform or simply use it for their own runs.

**How to use**

The software was built with four types of devices in mind:

* A server, which holds the data and does the processing
* A presenter, which is connected to a projector, showing the live map
* Multiple smartphones used my the run's assistants, to enter new round information
* A router to connect all devices

In our case, the server and presenter were one single device, a laptop connected both, the projector and router.
As server software, you can use packages like XAMPP, which you only have to install and it will
preconfigure all required software (the only requirements are php7+ and apache as server software).
And unzip the software files into your server root. (If you are using XAMPP, it is the htdocs directory
in your XAMPP installation folder.)

The router has to have the ability to let devices communicate within the same network, please check that.
AVM routers all seem to work fine, but you may have to test that first.
After connecting all devices to the router (which is not connected to the internet!) it's
the last step to find out the server's IP address. Entering it into a browser on any other connected device should
bring up the installation page (enter it the same way as you would normally enter google.com, but now it's something like
192.168.178.100).

After you are finished setting everything up, assistants can log in via the login button (bottom right corner) on their
devices. Phase one begins now, which includes entering all data into the software.
After that, the administrator logs in and revokes the assistant's permission to enter or edit user data. This
is to assure that no one accidentally deletes a runner or alters some other data. The administrator can revoke their permission
by clicking on the red banner on his sidebar. From now on, only the administrator is able 
_Until that point, the administrator account should not have been used._

Phase two is the actual run, assistants are able to add rounds to runners sequentially. This can either be done in real time
or in a fixed interval, e.g. every 30 minutes.

After the run has finished, the administrator is able to download the donator and runner data, which
includes the rounds ran per runner and per group, the donators, and how much they donated, already calculated. 