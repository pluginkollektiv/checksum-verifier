# Checksum Verifier #
* Contributors:      pluginkollektiv
* Tags:              security, md5, hash, checksum, scan, malware, SoakSoak
* Donate link:       https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8CH5FPR88QYML
* Requires at least: 3.8
* Tested up to:      4.6
* Stable tag:        trunk
* License:           GPLv2 or later
* License URI:       http://www.gnu.org/licenses/gpl-2.0.html


Verifies MD5 checksums of WordPress core files, sends e-mail warning in case of threat.


## Description ##
*Checksum Verifier* calculates MD5 checksums for all existing WordPress core files and checks them against official checksums. It thus will detect and react upon any unrecognized modifications made on your WordPress core system (for example [SoakSoak malware](http://blog.sucuri.net/2014/12/soaksoak-malware-compromises-100000-wordpress-websites.html)).

*Checksum Verifier* runs a daily check on MD5 checksums. In case an unexpected result is detected in core, the plugin will send an e-mail with a list of affected files to site administrators (or super-admins in WordPress multisite). The first check-up will be executed during plugin activation. Regular core updates won’t cause any alarm, of course. Only in case core files have been compromised and your site might have been hacked, the plugin will notify you.

*Checksum Verifier* works as a "silent" background process and will only bother you in case of unexpected file modifications that could potentially threaten your core install.

Better security with WordPress.


### Usable Hooks ###
* [checksum_verifier_ignore_files](https://gist.github.com/sergejmueller/59c014d82347215784f4)

### Memory Usage ###
* Back-end: ~ 0,06 MB
* Front-end: ~ 0,01 MB

### Available Languages ###
* English
* Deutsch
* Русский

### Support ###
* Community support via the [support forums on wordpress.org](https://wordpress.org/support/plugin/checksum-verifier)
* We don't handle support via e-mail, Twitter, GitHub issues etc.

### Contribute ###
* Active development of this plugin is handled [on GitHub](https://github.com/pluginkollektiv/checksum-verifier).
* Pull requests for documented bugs are highly appreciated.
* If you think you’ve found a bug (e.g. you’re experiencing unexpected behavior), please post at the [support forums](https://wordpress.org/support/plugin/checksum-verifier) first.
* If you want to help us translate this plugin you can do so [on WordPress Translate](https://translate.wordpress.org/projects/wp-plugins/checksum-verifier).

### Credits ###
* Author: [Sergej Müller](https://sergejmueller.github.io/)
* Maintainers: [pluginkollektiv](http://pluginkollektiv.org/)
* English Translator: [Caspar Hübinger](http://glueckpress.com)
* Russian Translator: [Sergej Müller](http://wpcoder.de)


## Installation ##
* If you don’t know how to install a plugin for WordPress, [here’s how](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

### System Requirements ###
* WordPress 3.8 and higher


## Changelog ##
### 0.0.1 ###
* *Checksum Verifier* goes live
