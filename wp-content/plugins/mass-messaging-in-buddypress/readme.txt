=== Mass Messaging in BuddyPress ===
Contributors: ElbowRobo
Donate link: http://eliottrobson.co.uk/portfolio/mass-messaging-in-buddypress/
Tags: mass, messaging, buddypress, users, members, groups, blogs
Requires at least: 3.0.0
Tested up to: 4.4
Stable tag: 2.1.3
License: GPLv2 or later

Ever wanted to send a message to many people at once? Now you can, introducing - Mass Messaging.

== Description ==

This plugin is for BuddyPress, it adds a dashboard menu and a tab in the messages section. Once you navigate into the messages section and click the "Mass Messaging" tab you have access to all the options which you chose in the dashboard.

Including mass messaging to:

* Members
* Members of Groups
* Members of Blogs (Sites)

And:

* Select all buttons to allow mass messaging to all members easily.

In this page you also see 'subject' and 'description' just like on the Buddypress compose page.

== Installation ==

1. Upload the folder `Mass Messaging in Buddypress` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Change the settings in the dashboard under Settings -> Mass Messaging
1. Navigate through to the messages section in the frontend to find the mass messaging page

== Frequently Asked Questions ==

= It's not loading, what's wrong? =

Firstly, this is only for Wordpress (Network too!) running Buddypress. Check you have these installed correctly. Then, check you have activated the plugin.

= What does "Send as single thread?" mean? =

With this ticked, a single message will be sent with everyone as a recipient.
With this unticked, each member will receive an individual message from you.

= Sending to more than 100 members in a single thread is currently not supported =

Unfortunately it is not currently possible to message more than 100 people in a single thread. This is to ensure that the system remains stable. A future version should hopefully include support for this.

== Screenshots ==

1. The tabbed location of the mass messaging front end.
2. The tabs and display once in the mass messaging page.
3. The admin settings area.

== Changelog ==

= 2.1.3 =
* Improving compatibility with more themes

= 2.1.2 =
* Shows success notification once sent all messages

= 2.1.1 =
* Fixing support for very large sites
* Fixes a critical defect
* Performance improvements

= 2.1.0 =
* Ajax sending to support large sites
* Simplified reordering (drag & drop)
* Group specific access
* Sent messages view count

= 2.0.2 =
* Show hidden groups

= 2.0.1 =
* Fix a few formatting inconsistencies
* Long lists include scroll-bars
* Allow re-ordering of options

= 2.0.0 =
* Completely rewritten
* Support for WP 4.3.1 and BP 2.4.0
* Support for translations (to be added later)

== Upgrade Notice ==

= 2.1.1 =
Fixes a critical defect introduced in 2.1.0

= 2.0.0 =
Completely rewritten to support WordPress 4.3.1 and BuddyPress 2.4.0

== Donations ==

If you want to find more information about the plugin head over to [the official plugin page](http://eliottrobson.co.uk/portfolio/mass-messaging-in-buddypress/ "eliottrobson.co.uk official page for Mass Messaging in Buddypress").

== Planned Features ==

* E-Mail fallback when PMs are disabled
* Mass messaging page on group admin