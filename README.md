# notime_ical_sync
Synchronize Notime.ch shifts from the picking system via icalendar to any device.

# How to add this to my phone?
Create your personal link [here](https://tttttx2.github.io/notime_ical_sync)

Simply login to google calendar on your desktop and go to "add calendar" --> "From URL" and enter the URL containing your credentials.

http://[placeholder].execute-api.eu-central-1.amazonaws.com/index.php?user=[placeholder]&pass=[placeholder]&token=[placeholder]

# Deployment
This is deployed using bref on amazon AWS as a serverless application. Currently using the free tier, so it might break any moment. Please do set a very low refresh interval if possible.

# Known limitations
Only events for the next few weeks will be displayed. Events from past weeks will be automatically removed, depending on your client. It's thus not a good idea to use this as a log of when you worked. It could help out in planning for the future though without having to go back to the app to check if you're actually working that specific day or if you can go out to enjoy the evening.

# Disclaimer
This is provided without any warranty at all. If it breaks, it breaks. If anything happens it's completely your own fault.
