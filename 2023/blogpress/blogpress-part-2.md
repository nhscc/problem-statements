# BDPA NHSCC 2023 Sample Problem Statement (part 2)

> See also: [API documentation](https://hsccjcat4d54.docs.apiary.io)

PressWord Software, the entity that contracted you to build their content
management system, applauds its successful rollout! Feedback indicates users are
satisfied with your app UX and performance data shows response time tail
latencies are very low. But your contractor has identified some changes they
want.

> We're looking for feedback! If you have any opinions or ideas, contact us on
> Slack.

🚧 🚧 To avoid disqualification, please take note of the following:

- We know how to use Git and other tools. If we notice any commits, patches,
  uploads, file transfers, chatter, etc external to your immediate team members
  during this phase of the competition, your team will be disqualified. Unlike
  PS1, **only the three to five students on your competition team—and no one
  else—can work on PS2.**
- Your solution’s landing page must be available at `http://127.0.0.1:3000`
  (localhost) on your team's AWS WorkSpace. **Judges must not have to type in
  anything other than `http://127.0.0.1:3000` to reach your app.**
- Your solution’s source code must be located at `%USERPROFILE%\Desktop\source`
  on your team's AWS WorkSpace. You can have other files located elsewhere, so
  long as they are also visible from the aforesaid directory.
- HTTP requests to the API must be sent with an `Authorization` header (`Key`
  header is deprecated). See the
  [API documentation](https://hsccjcat4d54.docs.apiary.io) for details.

There are ten (10) changes in total. Three (3) ✨changes✨ are worth more points
than the others:

## Change 1

**`blogger`s (and `administrator`s) can now change their email address and/or
password.**

However, `administrator`s cannot change another `administrator`'s email or
password. Also, changing a `blogger`'s email address must not change their
blog's [URL](./blogpress-part-1.md#requirement-9).

## ✨Change 2✨

**`blogger`s (and `administrator`s) can now change their blog's name.**

`blogger`s can only change the name of their own blog. `administrator`s can
change the name of any `blogger`'s blog.

Changing a blog's name must also change their blog's
[URL](./blogpress-part-1.md#requirement-9) to match.

## Change 3

**`blogger`s can now select which
[page](./blogpress-part-1.md#creating-and-editing-a-page) is the
[root page](./blogpress-part-1.md#the-root-page).**

A blog's ["root" or landing page](./blogpress-part-1.md#the-root-page) is the
[page](./blogpress-part-1.md#creating-and-editing-a-page) that is displayed when
a user navigates to the blog's root URL. Before this change,
[the home page was always the root page](./blogpress-part-1.md#requirement-4).
After this change, the home page is only the root page _by default_; `blogger`s
can change the root page to any other page in their blog.

For example, if a `blogger` with a blog named "cool-blogio" designates the
"contact" page their blog's root page, then navigating to
https://localhost:3000/cool-blogio should display the same page as
https://localhost:3000/cool-blogio/contact.

## ✨Change 4✨

**Authenticated users no longer have or need usernames. Only email addresses and
passwords are required to login.**

Usernames must be removed from your app's frontend entirely. For example: when
logging in, users must use their email address and password. When registering,
users must no longer be asked to provide a username. When deriving a
[digest value](https://developer.mozilla.org/en-US/docs/Glossary/Digest) to be
sent to the API, only the email address and password (email + password) should
be used; see
[the API documentation](https://hsccjcat4d54.docs.apiary.io/#/reference/0/user-endpoints/users-username-or-email-auth-post)
for further details.

## Change 5

**A banner should be added to the footer of all
[Blog view](./blogpress-part-1.md#requirement-2)s.**

The banner text must read "powered by BlogPress", be added as an aesthetically
pleasing footer to all [Blog view](./blogpress-part-1.md#requirement-2)s, and
should link back to the [Home view](./blogpress-part-1.md#requirement-2).

## ✨Change 6✨

**Associated active sessions must be manually expired when users leave a page.**

When a user closes their browser, or otherwise navigates away from a blog page,
the active session associated with that user, blog, and page should be
[_manually expired_](https://hsccjcat4d54.docs.apiary.io/#/reference/0/blog-endpoints/blogs-blog-name-pages-page-name-sessions-session-id-delete).

If your solution was not doing this already, then the number of active sessions
associated with a page only decreased when active sessions would automatically
expires in 30 seconds after not being renewed. With this change, the number of
active sessions should update much more quickly all across your app. That is:
shortly after the user leaves a page and, hence, stops being an "active" viewer
of that page, your solution must attempt to
[manually expire](https://hsccjcat4d54.docs.apiary.io/#/reference/0/blog-endpoints/blogs-blog-name-pages-page-name-activity-delete)
the active session associated with said page.

> [Hint.](https://dev.to/amersikira/top-3-ways-to-easily-detect-when-a-user-leaves-a-page-using-javascript-2ce4)

> [Another hint.](https://developer.mozilla.org/en-US/docs/Web/API/Beacon_API)

## Change 7

**`blogger`s (and `administrator`s) can rename their blog's pages.**

Previously, `blogger`s could only create or delete pages. Now, `blogger`s can
rename pages without losing their contents.

## Change 8

**`administrator`s can now delete users. Any user can also delete their own
account.**

Deleting a user will automatically (at the API level) delete that user's blog
and its pages. Your solution must not allow `administrator`s to delete other
`administrator`s.

Deleted users who are logged in must be forced to log out "immediately" (i.e. as
soon as the next HTTP response from the server).

Additionally, any user (_including `administrator`s_) can now delete their own
account. A user deleting their own account has the same effect as an
`administrator` deleting their account.

## Change 9

**Users that are banned while logged in are now forced to logout immediately.**

Before this change, users who are banned while their session is still valid
could still make changes to their blog. With this change, once an
`administrator` bans a user, that user must be logged out from your application
"immediately" (i.e. as soon as the next HTTP response from the server).

## Change 10

**All `administrator` actions are now logged. The audit log of `administrator`
actions can be viewed by any `administrator`.**

Every action taken by an `administrator` (i.e. creating, deleting, and banning
things) is now logged to an "administrator audit log". This audit log can be
viewed at any time by any `administrator`. Each entry in the audit log must
indicate:

- The time the action occurred
- The email address of the `administrator` who took the action
- A short description of the action
- The email address of the user and/or the blog/page name of the affected user,
  blog, or page where applicable

> The API will not store this information for you. You must store and query this
> information locally within your application.
