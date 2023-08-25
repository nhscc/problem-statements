# BDPA NHSCC 2021 Problem Statement (part 2)

> See also: [API documentation](https://hscckhug3eb6.docs.apiary.io)

BDPA Media Conglomerate LLC applauds Barker's successful rollout! Feedback
indicates users are satisfied with your app UX and performance data shows
response time tail latencies are very low. But your contractor has identified
some changes they want implemented.

> We're looking for feedback! If you have any opinions or ideas,
> [start a discussion](https://github.com/nhscc/problem-statements/discussions/new).

There are 10 changes:

## Change 1

**Barks can now be made _private_ or _public_.**

Users will have the option to toggle individual Barks as _public_ or _private_.
Public Barks are normal Barks. Private Barks, on the other hand, are only
visible to the `authed` user that created them and those in that user's
[Pack](./barker-part-1.md#requirement-3).

The default for all Barks is _public_.

## Change 2

**Home view now shows _public_ Barks from followed users _and the users they
follow_.**

The [Home view](./barker-part-1.md#requirement-2) will now show a list of Barks
from
[directly _or indirectly_ followed users](./barker-part-1.md#requirement-9).
That is: if user A follows user B and user B follows user C, then user A will
see Barks from both **users B and C** in the new version.

Previously, this view only showed Barks from _direct_ follows first. Keeping
with the above example, user A only sees Barks from user B and **not user C** in
the old version. Private Barks should only be visible to the user that owns them
and those in the owner's [Pack](./barker-part-1.md#requirement-3).

## Change 3

**The Bookmark view will include visual separators at certain intervals.**

Previously, the [Bookmark view](./barker-part-1.md#requirement-4) showed a list
of Barks in descending order of bookmarked time. Now, the view will show the
same list in the same order except there will be added visual (UI) separators
between Barks at the following intervals: _today_ (within the past 24 hours),
_this week_ (within the past 7 days), _this month_ (within the past 30 days),
_earlier_ (30 or more days ago).

An ASCII UI example:

<!-- prettier-ignore-start -->

```markdown
--- Today ---
... (Barks that were bookmarked within the last 24 hours)...
--- This Week ---
... (Barks that were bookmarked within the last 7 days)...
--- This Month ---
... (Barks that were bookmarked within the last 30 days)...
--- Earlier ---
... (Barks that were bookmarked later than 30 days ago)...
```

<!-- prettier-ignore-end -->

Additionally, there will be some navigation element that lets users quickly jump
between these intervals.

## Change 4

**Added visual indication when reaching end of Barks from followed users.**

Previously, when there were no more Barks to show an `authed` user in the
[Home view](./barker-part-1.md#requirement-2), the app began showing the user
the same Barks a `guest` would see. With this change, the user is now warned
when this content transition happens.

An ASCII UI example:

<!-- prettier-ignore-start -->

```markdown
... (last of the followed Barks)...
--- You've reached the end of your followed Barks    ---
--- but here are some Barks from other Barker users! ---
... (first of the remaining Barks)...
```

<!-- prettier-ignore-end -->

## Change 5

**Redirect `guest` users to login when they try to interact with Barks.**

Previously, only `authed` users could interact with Barks using likes ❤️,
rebarks 📢, bark-backs 🐺, etc. With this change, `guest` users can now attempt
these same interactions, except they are first redirected to the
[Auth view](./barker-part-1.md#requirement-5) when they try. If the user
authenticates successfully, they are redirected back to their Bark to complete
their attempted operation.

For example, if a `guest` user clicks the rebark button on a Bark, it should let
them login, become an `authed` user, and complete their rebark.

## Change 6

**Added visual indication differentiating Barks from rebarks.**

Previously, it wasn't required to differentiate between a brand new Bark and a
Bark created by rebarking.

With this change, all users will be able to tell when a Bark is brand new and
when it's rebarked and from whom. Rebarked Barks will now have some visual
indication in the UI 1) that they are Rebarks and 2) who the owner of the
original rebarked Bark is.

## Change 7

**`authed` users can now use hashtags in their Barks.**

`authed` users can include _hashtags_ as part of their Barks. Hashtags are
strings of number and letters (no spaces) that begin with a `#`, e.g.
`This is the bark #SuperCoolBarks`. Hashtags will be rendered in the UI as links
that, when clicked, redirect the user to a [search result](#change-9) for that
hashtag.

## Change 8

**New view: the Profile view.**

Previously, no user profile view was required. With this change, clicking on a
username in the app will redirect to the user's profile page, which shows a list
of Barks made by that user in descending chronological order. It should also
show the user's name and any other relevant information.

When a user is viewing their own profile, they have the option to change their
email address, phone number, password, or delete their account. When a user
decides to delete their account, 1) they should no longer be allowed to login
_or recover their password_ and 2) the user and all their Barks will have their
[`deleted` flags set in the API](https://hscckhug3eb6.docs.apiary.io/#/data-structures/0/bark).

## Change 9

**Home view now allows users to perform custom searches for Barks.**

Users can now search for Barks using [hashtags](#change-7) or containing
specific strings using a UI search bar. The
[Home view](./barker-part-1.md#requirement-2) will filter the Barks it displays
based on the strings provided.

## Change 10

**Home view now shows the top five most mentioned users and hashtags.**

The [Home view](./barker-part-1.md#requirement-2) will display the five most
mentioned users and the five most used [hashtags](#change-7) in the system.
