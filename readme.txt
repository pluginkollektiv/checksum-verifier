=== Checksum Verifier ===
Contributors: pluginkollektiv
Tags: security, md5, hash, checksum, scan, malware, SoakSoak
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZAQUT9RLPW8QN
Requires at least: 3.8
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



Verifies MD5 checksums of WordPress core files, sends e-mail warning in case of threat.



== Description ==

_Checksum Verifier_ calculates MD5 checksums for all existing WordPress core files and checks them against official checksums. It thus will detect and react upon any unrecognized modifications made on your WordPress core system (for example [SoakSoak malware](http://blog.sucuri.net/2014/12/soaksoak-malware-compromises-100000-wordpress-websites.html)).

_Checksum Verifier_ runs a daily check on MD5 checksums. In case an unexpected result is detected in core, the plugin will send an e-mail with a list of affected files to site administrators (or super-admins in WordPress multisite). The first check-up will be executed during plugin activation. Regular core updates won’t cause any alarm, of course. Only in case core files have been compromised and your site might have been hacked, the plugin will notify you.

_Checksum Verifier_ works as a “silent” background process and will only bother you in case of unexpected file modifications that could potentially threaten your core install.

Better security with WordPress.


= Usable Hooks =
* [checksum_verifier_ignore_files](https://gist.github.com/sergejmueller/59c014d82347215784f4)


= Memory Usage =
* Back-end: ~ 0,06 MB
* Front-end: ~ 0,01 MB


= Available Languages =
* English
* Deutsch
* Русский


= System Requirements =
* WordPress 3.8 and higher


= Donations =
* Via [Flattr](https://flattr.com/t/1628977)
* Via [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZAQUT9RLPW8QN)


= Author =
* [Twitter](https://twitter.com/wpSEO)
* [Google+](https://plus.google.com/110569673423509816572)
* [Plugins](http://wpcoder.de)


= Translators =
* English: [Caspar Hübinger](http://glueckpress.com)
* Russian: [Sergej Müller](http://wpcoder.de)




== Changelog ==

= 0.0.1 =
* *Checksum Verifier* goes live