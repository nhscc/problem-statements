# BDPA NHSCC 2022 Problem Statement (part 1)

> See also: [API documentation](https://hsccebun98j2.docs.apiary.io)

With distributed file storage and synchronization services like Google Drive
overtaking more traditional desktop applications, BDPA Cloud Services (BCS) has
decided to roll out a web version of its popular word processing software
they're calling _BDPADrive_. Your team is one of many competing to rebuild BCS's
aging word processing application into a sleek modern web app.

<details><summary>Summary of requirements (15 total)</summary>

The app supports two user types: guests and authenticated users. Authentication
occurs via the API. Users interact with the app through the creation and
management of files and folders.

The app has at least four views: _Explorer_, _Editor_, _Dashboard_, and _Auth_.
The Explorer view acts as the homepage of the app and the starting place for
viewing user files and their preview thumbnails. The Editor view is where users
author and edit their files. The Dashboard view is where users can configure
their accounts and view user-specific information. The Auth view is used for
handling authentication as only authenticated users can access the other views
and create files.

Note that time-based data in the API is represented as the number of
milliseconds elapsed since January 1, 1970 00:00:00 UTC. See
[the API documentation](https://hsccebun98j2.docs.apiary.io/#/introduction/tips-for-debugging)
for details. Further note that you **must** use the API to complete this problem
statement, including interacting with data from other chapters, though you may
consider a hybrid approach where you have your own database storing non-API
data.

</details>

> We're looking for feedback! If you have any opinions or ideas, contact us on
> Slack.

🚧 🚧 To avoid disqualification, please take note of the following:

- Your solution’s landing page must be available at `http://127.0.0.1:3000`
  (localhost)
- Your solution’s source code must be located at `%USERPROFILE%\Desktop\source`
- HTTP requests to the API must be sent with an `Authorization` header (`Key`
  header is deprecated). See the
  [API documentation](https://hsccebun98j2.docs.apiary.io) for details.

## Requirement 1

**Your app will support 2 types of users: guest and authed.**

### Guests

- Are unauthenticated users (i.e. don't have to login).
- Can only access the [Auth view](#requirement-5).

### Authed (Authenticated)

- Are authenticated users (i.e. users that have already logged in).
- Can access every view.
- Can access search functionality.
- Can access a filesystem overview.
- Can create, view, and edit owned files and folders.
- Can access a personalized dashboard.

## Requirement 2

**Explorer view: `authed` users create new text files and explore their
filesystem.**

This view allows `authed` users to see all of their files and folders in one
place, including name, ownership information, creation time, modification time,
file size, and a preview thumbnail (for text files). It purpose is very similar
to the File Explorer in Windows, or the My Files app on Android.

Users can click on files to [edit them](#requirement-3) and on folders to view
their contents, which can include other files and folders. Users can also rename
files and folders from this view, change the ownership of files to a different
user, [create](#requirement-3) and delete files and folders, and move files and
folders into and out of other folders. For text files specifically (so: not
symlinks), users can add and remove tags from this view.

Users can only see and modify files and folders that they own.

Users can sort files and folders by their creation time, modification time, file
size, or name (default).

### Creating A New File Or Folder

`authed` users can create their own files and folders. Files contain text
content (Markdown) or are symlinks. Folders contain files and other folders.

When creating a new folder, an `authed` user must provide the following:

- The name of the folder.

When creating a new text file, an `authed` user must provide the following:

- The name of the file.
- Any tags describing the file.
  - Tags are single alphanumeric words that ascribe additional meaning to files.
    Tags are handled in a case-insensitive manner. Each file can have between
    zero (0) and five (5) tags at a time.
- The text contents of the file.
  - Files are limited to 10KiB of text content.
  - Users use the [Editor view](#requirement-3) to add/modify a file's text.

`authed` users can also create special file types called
[_symbolic links_](https://en.wikipedia.org/wiki/Symbolic_link) or "symlinks".
Symlinks are placeholders that point to other files. Clicking a symlink has the
same effect as clicking on the file it's pointing to. When creating a symlink,
an `authed` user must provide the following:

- The name of the symlink.
- The
  [`node_id`](https://hsccebun98j2.docs.apiary.io/#/data-structures/0/file-node)
  of the file or folder that the symlink points to.

A symlink pointing to an invalid file or folder is considered "broken". A broken
symlink is a symlink that doesn't do anything when clicked. It must be evident
in the UI when a symlink is broken. A symlink must be considered broken under
the following circumstances:

- It points to a file or folder that the user does not own.
- It points to a file or folder that does not exist.
- It points to nothing.
- It points to itself.

### File Previews

When displaying text files in this view, users must see a thumbnail image
previewing the contents of each file. This thumbnail will be generated from the
contents of the text file, if any. Note that the thumbnail does not have to
include the entirety of the text file, it can be cropped to only include the
first few lines of content and/or whatever amount satisfies your best judgement.

Since a file's text content is written in
[Markdown](https://www.markdownguide.org/getting-started), your app will have to
[use a library to render the Markdown into HTML](#requirement-3). After it is
rendered, your app will have to turn that HTML into an image using another
library. Research and explore which library is best for your purposes.
[Here are some suggestions to start with](https://openbase.com/categories/js/best-javascript-html-to-image-libraries).
You may also find the
[`object-fit` and `object-position`](https://www.digitalocean.com/community/tutorials/css-cropping-images-object-fit)
CSS properties useful.

> It is recommended you use a library that works exclusively in the browser that
> can generate images on the fly, rather than something that requires a backend
> (Express/PHP/Python/etc) or something fancy like puppeteer or phantomjs.
> However, you are free to come up with whatever solution works for your app.

## Requirement 3

**Editor view: `authed` users view, author, and edit text files.**

Clicking on a text file in the [Explorer view](#requirement-2), or attempting to
create a new text file, leads to this view. The Editor view is where users can
view detailed information about their text files, author new text files, and
edit the content or properties of existing text files.

Since a file's text content is written in
[Markdown](https://www.markdownguide.org/getting-started), your app will have to
use a library to render the Markdown into HTML, which you can then display to
your users. Research and explore which Markdown library is best for your
purposes.
[Here are some suggestions to start with](https://npmcompare.com/compare/markdown,markdown-it,marked,remarkable,showdown).

From this view, users must also be able to view and modify the tags of the file,
rename the file, and delete the file.

Users can only rename and delete files that they own.

Users must be able to "save" their changes by pressing the save button. Changes
to text files should be saved automatically every so often.

> It is recommended that your app checks that the current user and client
> [owns the lock](#requirement-6) on a text file before you allow a "save"
> operation to go through.

## Requirement 4

**Dashboard view: `authed` users view information about themselves.**

From this view, `authed` users can:

- Update their email addresses.
- Change their passwords.
- Delete their accounts.
  - Deleting an account must also trigger the deletion of all the files and
    folders that the deleted user owned.

Users must also be able to see the total amount of storage their account is
using. That is: users can see the sum of the sizes of all the files that they
own, not including symlinks.

## Requirement 5

**Auth view: register new users and authenticate existing users.**

`guest` users can use this view to authenticate (login) using their _username_
and their _password_. This sensitive information is referred to as a user's
_credentials_.

Users must be authenticated before they can access any other views.

### Authenticating Credentials (Login)

Your app must use
[the API](https://hsccebun98j2.docs.apiary.io/#/reference/0/user-endpoints/users-username-auth-get)
to authenticate the `guest` user _instead of_ retrieving the user's credentials
from a local database. Your app will do this by sending the API a
[digest value](https://developer.mozilla.org/en-US/docs/Glossary/Digest) derived
from the username and password provided. See
[the API documentation](https://hsccebun98j2.docs.apiary.io/#/reference/0/user-endpoints/users-username-auth-get)
for more details.

### Revoking Authentication (Logout)

If `authed`, the user can choose to _logout_, after which your app will treat
them like any other `guest` user. Logging a user out does not require a call to
the API.

### Registering New Credentials

There is an open registration feature `guest` users can use to register a new
account. When they do, they must provide the following where required:

- Desired username <sup>\<required\></sup>
  - Must be alphanumeric (dashes and underscores are also allowed).
- Email address <sup>\<required\></sup>
- Password <sup>\<required\></sup>
  - Password strength must be indicated as well. Weak passwords will be
    rejected. A weak password is ≤10 characters. A moderate password is 11-17
    characters. A strong password is above 17 characters.
- The answer to a simple CAPTCHA challenge of some type <sup>\<required\></sup>
  - Example: `what is 2+2=?`
  - Teams must not call out to any API for this. Teams must create the CAPTCHA
    manually.

Your app must use
[the API](https://hsccebun98j2.docs.apiary.io/#/reference/0/user-endpoints/users-post)
to create the new user _instead of_ storing the user's information locally.

### Additional Constraints

- Usernames and email addresses must be unique. That is: no two users can have
  the same username or email address. This is enforced at the API level.
- `guest` users will be prevented from logging in for 1 hour after 3 failed
  login attempts. `guest` users will always see how many attempts they have
  left.
- `guest` users will have the option to use
  [remember me](https://www.troyhunt.com/how-to-build-and-how-not-to-build)
  functionality to maintain long-running authentication state. That is: if a
  `guest` logs in and wants to be "remembered," they will not be logged out
  until they manually log out.
- You must use the API to create new users, authenticate and store their
  credentials, and track any relevant metadata. See
  [the API documentation](https://hsccebun98j2.docs.apiary.io/#/data-structures/0/user)
  for details on what the API will store for you. Anything not storable at the
  API level can be stored locally by your solution.

## Requirement 6

**"Interceding update" conflicts must be resolved via a
[locking mechanic](https://en.wikipedia.org/wiki/File_locking).**

What happens if two instances of the app try to edit the same file at the same
time? This is known as a
[race condition](https://en.wikipedia.org/wiki/Race_condition#Software), and
your app must be able to resolve them.

To indicate that a
[file](https://hsccebun98j2.docs.apiary.io/#/data-structures/0/file-node)'s text
is being actively edited—which prevents race conditions like one user editing a
file's text in the middle of another user (or the same user) editing the same
file—the `lock` property must be used. This property consists of a
[Lock object](https://hsccebun98j2.docs.apiary.io/#/data-structures/0/lock) with
three sub-properties: `user`, `client`, and `createdAt`.

### Example

Suppose a user (e.g. "User1") has the app open in two different browser tabs
(e.g. "Tab1" and "Tab2"). User1 opens a text file for editing in the first tab.
User1 also opens the same file for editing in the second tab.

In the second tab, User1 makes changes to the text file and saves them,
triggering a PUT request to the API. At this point, your app could set the
file's `lock` property to the "User1-Tab2" user-client combination, e.g.
`{ "user": "User1", "client": "tab-2-random-identifier", "createdAt": 1234567890 }`.

Back in the first tab, User1 makes different changes to the same text file and
tries to save them. At this point, before sending the PUT request, your app
could check to see if there is a `lock` property matching the "User1-Tab1"
user-client combination.

Remember that the second tab set `lock` to the "User1-Tab2" combination earlier.
This tells the app that the first tab does not "own the lock" (the second tab
does), which means any changes made in the first tab could overwrite changes in
the second tab if saved.

> If `lock` is `null`, that must mean no one owns the lock!

So which tab's changes should "win" and which should be discarded/overwritten?

There are several ways you might resolve this conflict. One viable strategy is
through explicit confirmation. Your app might first confirm with the user that
going through with the save in the first tab could overwrite earlier changes. If
the user accepts this, then the PUT request is sent, the changes from the first
tab win, the changes from the second tab are overwritten, and the `lock` is
updated to "User1-Tab1" (now the first tab owns the lock). If instead the user
rejects this, no PUT request is sent, the changes from the first tab are
discarded, the changes from the second tab are left alone ("win"), and the
second tab retains ownership of the lock. Either way, the conflict is resolved.

> This means your app is also responsible for setting the `lock` property to
> `null` when your user is done editing the `file` node. What happens if the
> user suddenly exits the app before saving, or the browser crashes before your
> app has a chance to remove the lock? How might your app prevent a
> [deadlock](https://en.wikipedia.org/wiki/Deadlock) scenario? Does the
> suggested strategy above avoid deadlocks already?

## Requirement 7

**If a user does not remember their password, they can use email to recover
their account.**

If a user has forgotten their login credentials, they will be able to recover
their account by clicking a link in the recovery email sent to their address.
Your app will then allow them to set a new password.

<blockquote>

The app must not actually send emails out onto the internet. The sending of
emails can be simulated however you want, including outputting the would-be
email to the console. The app will not use an external API or service for this.
For full points, ensure you document how recovery emails are simulated when
submitting your solution.

</blockquote>

## Requirement 8

**A navigation element containing the BDPA logo, your app title, and a subset of
user data is permanently visible to users.**

In every view, a navigation element is permanently visible containing the BDPA
logo (downloadable
[here](https://bdpa.org/wp-content/uploads/2020/12/f0e60ae421144f918f032f455a2ac57a.png))
and the title of your app. For `guest` users, they also have the option to login
or sign up. For `authed` users, they will see their username which links to the
[Dashboard view](#requirement-4).

## Requirement 9

**`authed` users can search for files and folders they own.**

`authed` users can search for files and folders they own by name, by tag, by
text content, by type (file, symlink, or directory), by creation time, by
modification time, or some combination thereof. Results will appear in
descending order of modification time (for files) and creation time (for folders
and symlinks).

Users can start a search from any view in the app.

## Requirement 10

**Previews in the [Editor view](#requirement-3) update without a page refresh.**

In the [Editor view](#requirement-3) view, when editing the text of a file, the
changes should appear _live_ in the UI. The user must be able to see a live
preview of the text file they're writing side by side with the editor
([example](https://markdown-it.github.io/)). That is: the preview should update
as edits happen _without the page refreshing_ or the user doing anything extra
like pressing a "refresh" or "preview" button.

<blockquote>

This type of automatic updating is called _asynchronous_ or
"[ajaxian](<https://en.wikipedia.org/wiki/Ajax_(programming)>)" since it occurs
outside the usual
[_synchronous_ event flow](https://dev.to/lydiahallie/javascript-visualized-event-loop-3dif).
There are
[many](https://www.encodedna.com/javascript/practice-ground/default.htm?pg=auto-refresh-div-using-javascript-and-ajax)
[examples](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch#uploading_json_data).
One way to implement asynchronous features is by using
[input events](https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/input_event)
to trigger re-renders on input changes.

</blockquote>

## Requirement 11

**The app will be performant.**

[The amount of time the application takes to load and sort data, display information, and act on input is progressively penalized](https://www.websitebuilderexpert.com/building-websites/website-load-time-statistics).
That is: the teams with the fastest load times will earn the most points and the
teams with the slowest load times will earn zero points from this requirement.

Average (median) is used to calculate load times. Measurements will include
initial page load times and, depending on the other requirements, various other
frontend UI response times.

> [Tail latencies](https://robertovitillo.com/why-you-should-measure-tail-latencies)
> (e.g. on startup with a
> [cold cache](https://stackoverflow.com/questions/22756092/what-does-it-mean-by-cold-cache-and-warm-cache-concept))
> are ignored. Only averages are considered.

> [FOUC](https://stackoverflow.com/a/43823506/1367414) may also be penalized.

> To maximize performance, consider
> [caching](https://www.sitepoint.com/cache-fetched-ajax-requests)
> ([see also](https://web.dev/cache-api-quick-guide)) the result of data
> processing operations and using
> [range queries](https://hscc8udvc7gs.docs.apiary.io/#/introduction/pagination)
> to retrieve only the data your app hasn't yet processed.

## Requirement 12

**Results and lists of items displayed in the frontend UI will be paginated
where appropriate.**

[Pagination](https://www.smashingmagazine.com/2007/11/pagination-gallery-examples-and-good-practices)
is the strategy of showing a limited number of a large set of results and
providing a navigation element where users can switch to different "pages" of
that large set. A Google search result (which has multiple pages) is a good
example of pagination.
[Infinite scroll](https://infinite-scroll.com/demo/full-page), a specific
pagination implementation used by the likes of Facebook/Instagram and Twitter,
is another good example.

## Requirement 13

**Security: no [XSS](https://owasp.org/www-community/attacks/xss),
[SQLI](https://owasp.org/www-community/attacks/SQL_Injection),
[insecure database](https://haveibeenpwned.com), or
[other trivial security vulnerabilities](https://owasp.org/www-project-top-ten).**

The app will use modern software engineering practices that protect from common
[XSS](https://owasp.org/www-community/attacks/xss),
[SQL injection](https://owasp.org/www-community/attacks/SQL_Injection), and
[other security vulnerabilities](https://owasp.org/www-project-top-ten).
Specifically: **form inputs** and the like **will not be vulnerable to SQL
injection attacks. User-generated outputs _(like the titles/names of items and
the result of rendered Markdown)_ will not be vulnerable to XSS or similar
attacks.**

As for database security, passwords and other credentials used to authenticate
your users **must be stored in the API** and **NEVER stored locally!** Any other
sensitive information present in your local database can be hashed or encrypted
as you deem appropriate.

> You must forward login credentials to the API when attempting to authenticate
> a user. Users' passwords, if stored in your database or app in any form, will
> earn your team zero points for this requirement.

## Requirement 14

**The app will
[fail gracefully](https://getbootstrap.com/docs/5.0/forms/validation/#server-side)
when exceptional conditions are encountered.**

This includes handling API errors during fetch, login errors, random exceptions,
[showing spinners](https://getbootstrap.com/docs/4.4/components/spinners) when
content needs to load, etc.

> Every so often,
> [the API will respond with an `HTTP 555` error](https://hscc8udvc7gs.docs.apiary.io/#/introduction/rules-of-api-access)
> instead of fulfilling a request. Further, API requests and responses will be
> manipulated by the judges in an attempt to break the app. If at any time a
> user is presented with a _non-app_ error page or a completely blank screen for
> [more than a second or so](https://www.websitebuilderexpert.com/building-websites/website-load-time-statistics),
> your solution may lose points on this requirement.

## Requirement 15

**The frontend UI will be responsive to mobile, tablet, and desktop
[viewports](https://www.w3schools.com/css/css_rwd_viewport.asp).**

The app will be pleasant to the eye when viewed on a smartphone, tablet, and a
desktop viewport. The design and functionality will not "break" across these
viewports nor will the app become non-functional.

> Judges will view and interact with the app through
> [emulated phone and tablet viewports](https://developers.google.com/web/tools/chrome-devtools/device-mode).
> If the app breaks when viewed, it will lose points on this and other
> requirements. We recommend using
> [mobile-first software design principles](https://designshack.net/articles/mobile/mobilefirst).
