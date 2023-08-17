# BDPA NHSCC 2023 Problem Statement (part 2)

> See also: [API documentation](https://hscc3epkxdp7.docs.apiary.io)

BDPA Business Solutions was impressed with your demo rollout! Feedback indicates
users are satisfied with your app UX and performance data shows response time
tail latencies are very low. But your contractor has identified some changes
they want.

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
  long as they are also visible (e.g. soft-linked) from the aforesaid directory.
- HTTP requests to the API must be sent with an `Authorization` header (`Key`
  header is deprecated). See the
  [API documentation](https://hscc3epkxdp7.docs.apiary.io) for details.

There are ten (10) changes in total. Three (3) ✨changes✨ are worth more points
than the others:

## Change 1

**Your application must be updated to use the new V2 API exclusively.**

See
[the documentation](https://hscc3epkxdp7.docs.apiary.io/#/introduction/migration-guide)
for the new version of the API. You will need to update the base address you're
using from https://inbdpa.api.hscc.bdpa.org/v1 to
**https://inbdpa.api.hscc.bdpa.org/v2**. The documentation also includes a brief
migration guide.

> We will disable the V1 API **four hours into the competition**. If your
> submitted solution malfunctions without the V1 API, you will lose a
> significant amount of points.

## Change 2

**Full names are now required when registering new users, and user's full names
are now displayed prominently in the _full_
[Profile view](./inbdpa-part-1.md#requirement-3).**

Unauthenticated `guest` users must _never_ be allowed to see a user's full name
when accessing the [Profile view](./inbdpa-part-1.md#requirement-3). Users can
add their full name to their existing accounts, and can update their full name.

Your app must degrade gracefully when encountering a "legacy user," i.e. a user
created before this change was made and whose full name is therefore not in the
system.

## ✨Change 3✨

**Feed view: authenticated users can now post their own "articles" and see
articles posted by the users to which they're
[connected](./inbdpa-part-1.md#requirement-9).**

The Feed view lists articles from all the users to which the viewer is
[connected](./inbdpa-part-1.md#requirement-9) (i.e. first, second, and third
-order connections). Articles appear sorted in descending order of creation date
(most recent articles appear first) and include the article's title, a subset of
associated keywords, the number of views, and the number of active viewers
(sessions). Clicking on an article shows the full contents of the article
rendered as [Markdown](https://www.markdownguide.org/getting-started), along
with the article's keywords.

Users can also create new articles (which will appear in their own feed), edit
articles they created, and delete articles they created.

Similar to [opportunities](./inbdpa-part-1.md#requirement-4) (is that a hint?),
articles consist of a max 100 character title, at most 10 keywords (max 20
characters each), and up to 3,000 characters of Markdown contents. Your app will
have to use a library to render an article's Markdown contents into HTML when
users access it.

Unauthenticated users (`guest`s) cannot access this view.

As with the [Profile view](./inbdpa-part-1.md#requirement-3) and
[Opportunity view](./inbdpa-part-1.md#requirement-4): the total number of views
and number of active viewers be updated, and the displayed value in the view
must update, [asynchronously](./inbdpa-part-1.md#requirement-10).

Additionally, in the [Admin view](./inbdpa-part-1.md#requirement-5), the
system-wide statistics must now include the total number of articles.

## ✨Change 4✨

**Users can search/filter the articles that appear in the new
[Feed view](#change-3) by keyword.**

From within the [Feed view](#change-3), users can filter which articles are
displayed by keyword. The search functionality should be simple but entirely
functional. It does not need to be fancy.

> Hint: to keep things speedy and avoid getting rate limited, keep a **simple**
> local database of associations between articles and their keywords instead of
> constantly hitting the API, and implement **simple** search functionality
> using this database.

## Change 5

**When viewing an article, the web page title will now update to include said
article's name.**

That is: the title that the browser displays when loading an article will now
include some portion of the article's title upon accessing the view. The title
will return to normal once the user navigates to a different view.

## Change 6

**Authenticated users can now change their email and full name from within the
[Profile view](./inbdpa-part-1.md#requirement-3).**

Authenticated users can only change their own email address and/or full name.
However, by [impersonating users](./inbdpa-part-1.md#requirement-5),
`administrator`s can change the email or full name of any other
non-`administrator` account.

## Change 7

**`administrator`s can now demote `staff` accounts back into `inner` accounts.**

Note, however, that `administrator`s _cannot_ demote other `administrator`
accounts.

Demoting a user should not affect their opportunities, even if they are no
longer `staff` with control over opportunities that they created.

## Change 8

**The [_full_ Opportunity view](./inbdpa-part-1.md#requirement-4) is now capable
of showing more detailed statistics.**

Instead of simply showing how many active sessions are viewing an opportunity,
the [_full_ Opportunity view](./inbdpa-part-1.md#requirement-4) now shows how
many of the active sessions are authenticated users versus how many are
unauthenticated users.

As before, this data must refresh in the view
[asynchronously](./inbdpa-part-1.md#requirement-10). That is: the updated totals
must eventually appear within this view _without the page refreshing_ or the
user doing anything extra, like pressing a refresh button.

## ✨Change 9✨

**The [Admin view](./inbdpa-part-1.md#requirement-5) is now capable of showing
more detailed system-wide statistics.**

Leveraging
[improved session tracking in the new V2 API](https://hscc3epkxdp7.docs.apiary.io/#/data-structures/0/session),
the [Admin view](./inbdpa-part-1.md#requirement-5) now allows `administrator`s
access to a system-wide overview of user activity (sessions).

Put another way: `administrator`s can see a list of all sessions in the system,
along with information about the view and the user associated with each session.

Information displayed about the associated view must be:

- The name of the view (i.e. "home," "auth," "admin," "profile," "opportunity,"
  or "article") as a hyperlink leading to the associated profile, opportunity,
  article, or view

Information displayed about the associated user must be:

- The user's username as a hyperlink leading to their profile, if they're
  authenticated, or an indication that no user data is available if they're
  unauthenticated or if they're a guest

The idea is to give an overview of what each user is currently doing in the
system. For example:

```html
<ul>
  <li>...</li>
  <li>
    <a href="path/to/user-X/profile">user-X</a> is currently looking at
    <a href="path/to/user-Y/profile">user-Y's profile</a>
  </li>
  <li>...</li>
</ul>
```

Administrators can
[sort and filter](https://aspiringyouths.com/compare/difference-between-sorting-and-filtering)
this user activity data by:

- If available, the associated user's username, or their email if their username
  is not available
- Whether the associated user is authenticated, or if they are not (i.e. there
  is no associated user)
- The name of the view
- Raw session identifier (`session_id`)
- Raw user identifier (`user_id`)

This data must refresh in the view
[asynchronously](./inbdpa-part-1.md#requirement-10). That is: the updated totals
must eventually appear within this view _without the page refreshing_ or the
user doing anything extra, like pressing a refresh button.

> Hint: leverage your local database (instead of hitting the API) to quickly
> retrieve properties about an associated user whenever possible.

## Change 10

**`administrator`s can now delete users from the
[Admin view](./inbdpa-part-1.md#requirement-5). Any user can also delete their
own account from the [Profile view](./inbdpa-part-1.md#requirement-3).**

Deleting a user will automatically (at the API level) delete that user's
articles but not their opportunities.

Your solution must not allow `administrator`s to delete other `administrator`s.

Deleted users who are logged in must be forced to log out "immediately" (i.e. as
soon as the next HTTP response from the server).

In addition, any user (_including `administrator`s_) can now delete their own
account. A user deleting their own account has the same effect as an
`administrator` deleting their account.
