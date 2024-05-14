# BDPA NHSCC 2020 Problem Statement (part 1)

> See also: [API documentation](https://hsccdfbb7244.docs.apiary.io)

With many airports in a tough spot financially, BDPA Airports, Inc. wishes to
capitalize on the moment and expand their network of airport franchises into
various cities. Your team has been contracted by BDPA Airports, Inc to build a
secure web portal for clients to view and book flights arriving and departing
from your local BDPA airport.

<details><summary>Summary of requirements</summary>

Your app will consist of a web portal where users, _logged in or not_, can view
all of the flights arriving and departing from your local airport. Logged in
users can manage and modify their booked flights. You must query flight data by
making REST requests to the [airports API](https://hsccdfbb7244.docs.apiary.io).

There are three user _types_: **guests**, **customers**, and **admins**. Users
can only be one type at a time. Guests are the most common type of user. They
are users who have not logged in, so they only have limited access to your app.
Customers are guests who have logged in and gained access to their own personal
dashboard where they can view recent and upcoming flights, change settings, and
manage their personal data. Admins have elevated privileged across your app,
including managing users and tickets. There is also a special **root** user, a
special administrator that is the only user that can create and delete other
administrator users.

Note that time-based data in the API is represented as the number of
milliseconds elapsed since January 1, 1970 00:00:00 UTC. This includes arrival
and departure times. See
[the API documentation](https://hsccdfbb7244.docs.apiary.io/#/introduction/tips-for-debugging)
for details. Further note that you **must** use the API to complete this problem
statement, though you will likely require a hybrid approach where you have your
own database storing some data and using the API to retrieve other data.

</details>

> We're looking for feedback! If you have any opinions or ideas,
> [start a discussion](https://github.com/nhscc/problem-statements/discussions/new).

## Requirement 1

**Your app will support 3 types of users: guest, customer, and admin.**

### Guests

- Are unauthenticated users (i.e. don't have to login)
- Can view, search, and sort through a complete paginated listing of recent and
  future flights
  - This applies to _all_ flights in the system, including flights not landing
    at your airport
- Can book flights by buying tickets
  - Only if they're not on the FAA No Fly List
  - Will get a confirmation number for their flight
  - You should only allow bookings for flights that are landing at your airport
- Can view details of a ticket they've purchased
  - Must provide their last name and a confirmation code
  - Can cancel/refund the ticket if the flight has not yet taken off
- Can view any flight's information
- Can create a new customer account

### Customer

- Are authenticated users (i.e. must login)
- Can do everything a guest type user can do (except create a new account)
- Can view/modify their own private account dashboard/settings
- Can delete their account
- Can view a complete paginated listing of flights they've booked

### Admins

- Are authenticated users (i.e. must login)
- Can access the administration view
- Can create and delete customer accounts
- Can prevent certain people from purchasing flights from specific airlines
- Can view and modify other accounts' information (except their type)
- **Cannot** create or modify other admin accounts
- **Cannot** delete any admin accounts

Users can only be a single type. You are free to create other types.

Note the special **root** user. The root user is a special admin that can
create, modify, and delete other admin accounts, can never be deleted, and is
unique (i.e. there can only be one root user).

## Requirement 2

**Flights view: users can view, search through, and sort through a paginated
list of all "recent" and future flights.**

By "recent" we mean: flights will not show up in this view if their status is
`past`.

The public flight view shows the following information **for both arriving and
departing flights**:

- Scheduled time to arrive/depart
- City the flight is arriving from/departing to (e.g. Los Angeles)
- The name of the airline that runs the flight (e.g. American Airlines)
- The flight number (e.g. UA1664)
- The latest status of the flight
  - Possible statuses:
    - `past` (completed flight)
    - `scheduled` (future flight)
    - `cancelled`
    - `delayed`
    - `on time`
    - `landed` (but not at gate yet)
    - `arrived`
    - `boarding`
    - `departed` (finished boarding, left airport)

And if the flight is specifically a departure:

- The latest gate information is also shown
- The name of the airport departing flights is departing to (e.g. CHI for BDPA
  Chicago Airport)

> Note: you'll have to decide how to handle displaying flight data for flights
> that are not arriving/departing from your airport.

Anyone can see this view, including guests who are not logged in. By default,
all flights are shown. Arrivals and departures will be shown separately. All
flights will be searchable and sortable by flight number, airline, airport,
city, arrival/departure time. For example, users can sort by airline in
descending alphabetical order, sort by gate in ascending alphabetical order,
sort from arrival time in descending order, etc.

There will be a "book this flight" button(s) or some similar functionality that
sends customers and guests to a flight booking view so they can book the flight
they clicked on. However, **tickets can only be purchased if the flight is
scheduled to depart in more than 24-hours from the time of booking**.

> Warning: flights with status `past` will be deleted from the API after 7 days,
> so ensure your solution does not rely on past flights existing perpetually.
> Customers must still see a complete history of their past bookings where
> appropriate.

> Note: flights are generated by the system on a per-hour basis (limited to
> around 16 hours per day) up to 30 days in advance. There will be no flights
> that exist more than 30 days in the future, and every day will have around 8
> hours with no flights on the ground (and sometimes no flights in the air). You
> must account for this.

> Note: your team only has to worry about booking tickets for flights where
> `type=="departure"` where `landingAt` equals your airport. You do not have to
> worry about booking tickets for departures where `departedTo` equals your
> airport. These should be treated as you would any other arrivals.

### How Arrivals Work

Arrivals (i.e. flights where `type=="arrival"`) will be `status=="scheduled"`
until `departFromSender` time elapses, after which they'll become
`status=="on time"` as they fly in from `comingFrom` airport on their way to
`landingAt` airport. Alternatively, a flight may become `status=="cancelled"`
instead. A cancelled flight will no longer update in the system and can be
considered in its final state. This is the only point where a flight might be
cancelled. If `status!="scheduled"`, a flight will **never enter** the cancelled
state.

At some point before it lands, the flight may become `status=="delayed"` and/or
be assigned a gate; gates can be changed/reassigned at any time! Once
`arriveAtReceiver` time elapses, the fight will become `status=="landed"`, which
means the flight is on the ground but not yet at its gate. Once the flight pulls
up to its assigned gate, it will become `status=="arrived"` (the gate for this
flight will no longer change after this happens). At the end of the hour, the
flight will become `status=="past"` and its gate will change to `null`.

### How Departures Work

Departures (i.e. flights where `type=="departure"`) will be
`status=="scheduled"` until `departFromSender` time elapses, after which they'll
become `status=="on time"` as they fly in from `comingFrom` airport on their way
to `landingAt` airport. Alternatively, a flight may become `status=="cancelled"`
instead. A cancelled flight will no longer update in the system and can be
considered in its final state. This is the only point where a flight might be
cancelled. If `status!="scheduled"`, a flight will **never enter** the cancelled
state.

At some point before it lands, the flight may become `status=="delayed"` and/or
be assigned a gate; gates can be changed/reassigned at any time! Once
`arriveAtReceiver` time elapses, the fight will become `status=="landed"`, which
means the flight is on the ground but not yet at its gate. Once the flight pulls
up to its assigned gate, it will become `status=="arrived"` (the gate for this
flight will no longer change after this happens). After a few minutes, it will
become `status=="boarding"`. 10-15 minutes later, once `departFromReceiver` time
elapses, it will become `status=="departed"` as they fly on their way to
`departingTo` airport. A few hours after that, the flight will become
`status=="past"` and its gate will change to `null`.

## Requirement 3

**Booking view: users can book flights.**

A flight has at least the following components:

This view will ask the user **where they want to arrive at** (users can enter a
city, state, country, or airport) and **the date (day, month, year) of the
flight they're looking for**. The user is always departing from the airport that
your app was built to represent, so you do not need to ask where they're
departing from. After, the user will be presented with a list of flights (and
their prices) that meet these requirements. If the user arrived at this view
directly from the flights view, this step can be skipped since the desired
flight is already known.

> Note that your API key will only allow you to book flights that are departing
> from the airport that your app was built for. These flights will have
> `bookable==true`.

Next, the user will be required to fill out:

- Identifying information
  - [Name (first, optional middle, last), sex, birthday (day, month, year)](https://amstat.tandfonline.com/doi/full/10.1080/2330443X.2017.1389620)
  - Also: phone number, email address
- Provide payment information
  - Flight price is taken from the API
  - Card number, expiration date, CVC code, cardholder name, billing address and
    zip
  - The form will accept fake credit/debit card information. **Please do not
    ever input or store any real credit/debit card information ever.**
  - If they're logged in, the customer will be asked if they want to save this
    information. They should be able to reuse it later without having to retype
    everything.
- Select and confirm their seat
  - Seat numbers/letters, their prices, where the seats are on the airplane, and
    the currently selected seat will all be presented somehow by your app
  - A graphical seat-map would be most impressive
- How many bags they want to check and how many they want to carry on
  - The first carry-on bag is free, the second is $30
  - The first checked bag is free, the second is $50, and each subsequent bag is
    $100
- Users are limited to 2 carry-on bags and 5 checked bags

All the seats for a flight will be the same price which can be found in the
appropriate API response. Every flight has a total of 90 seats thanks to
pandemic restrictions.

Finally, the user will be able to review their purchase before confirming.
Afterwards, they will be directed to the ticket view for their flight. If
they’re logged in, the flight will be added to the user’s private account. Note
that users must book flights **at least 24 hours before the flight’s departure
time**. This means the flight must be `status=="scheduled"` and
`arriveAtReceiver` must be greater than 24 hours from now for customers to book
tickets (and, of course, `bookable==true` must be the case for that flight).

> Check out the
> [API data structure reference](https://hsccdfbb7244.docs.apiary.io/#/data-structures/0/vote)
> for example response schemas.

> Warning: flight information **must be retrieved using the API**. You can cache
> it locally after the fact, but it **must** come from the API originally. Only
> accessing your local database (and/or generating your own flight data) without
> using the API will disqualify your solution.

Feel free to add any other information necessary, but extra information cannot
be stored using the API. Hence, some data will be split between the API and your
own local database.

## Requirement 4

**Ticket view: users can view tickets.**

This view displays the digital version of a user's ticket. It shows the
following information at minimum:

- If the flight is an arrival or a departure
- Flight airline and number
- Destination (city, state, country, airport)
- Departure and arrival date and time
  - If this is updated by the API, it will be updated in the view asynchronously
- Passenger name
- Gate number
  - If this is updated by the API, it will be updated in the view asynchronously
- Confirmation number
- Current flight status

There are four ways a user might access this view:

- A customer clicks a link in their dashboard, allowing them to view their
  ticket
- A customer or guest finishes purchasing a ticket through the booking view,
  allowing them to view their ticket
- A guest enters their last name and confirmation number, allowing them to view
  that ticket (if it exists)
- An admin clicks a link in the administrator view, allowing them to view any
  ticket

## Requirement 5

**Dashboard: each customer will have access to their own private dashboard.**

When viewing their dashboard, customers are presented with the following
information at least once:

- **name**: the name of the user, like "Ray" or "Ray Tiles"
- **last_login_ip**: the IP address that was last used to login as this user
- **last_login_datetime**: a timestamp taken the last time the user was
  authenticated

You are free to display any other relevant information.

This dashboard will also show:

- Upcoming and past flights
  - Clicking on flights will navigate to the ticket view for that flight
  - The date, time, and location of departure and arrival will be displayed for
    each flight
  - For upcoming flights, the information displayed will be updated
    asynchronously so that changes to date, time, and location appear without a
    page refresh
- All of their own personal information

Customers can also:

- Update their stored information (email, address, etc)
- Change the default sorting order of flights in the flight view
- Save or remove credit/debit cards
- Choose the default automatic logout time: either 15 minutes, 5 minutes, or 1
  hour.
- Manually add flights booked previously as a guest to the customer’s list of
  past and upcoming flights
  - When a guest account books a flight, they get an confirmation number.
    Customers will be able to enter that confirmation number and, if the last
    name on the ticket matches the last name of the customer, that flight will
    become associated with their account as if they had purchased it while
    logged in

> Please do not ever input or store any real credit/debit card information ever.

## Requirement 6

**Dashboard: admins will have access to an administration dashboard.**

This dashboard will show:

- Number of tickets your airport sold in the last day, week, month, year, and
  all time
- Total gross profit your airport made in the last day, week, month, year, and
  all time

Admins can also:

- Create new customers
- View a list of all customers and modify their details
- View any ticket using its confirmation code or some other unique identifier
- Create new tickets
- Cancel tickets for upcoming flights
  - Cancelling a ticket will deactivate it in your app and allow the seat to be
    purchased by someone else. The ticket will show up on the dashboard/ticket
    as "cancelled" or "refunded" and the ticket will otherwise not be
    accessible.
  - This is irreversible, though the user could always buy another ticket.
- Search for a customer using any user information (email, address, name, etc)
- Search for tickets using any flight information (departing airport, arrival
  airport, etc)

## Requirement 7

**Guests can sign up for new customer accounts; admins can create new customer
accounts.**

There is an open registration feature. Anyone can register for a new customer
account. When they do, they must provide the following:

- Title
- First name <sup>\<required\></sup>
- Middle name
- Last name <sup>\<required\></sup>
- Suffix
- Date of birth <sup>\<required\></sup> (month, day, year)
- Sex <sup>\<required\></sup>
- Street address <sup>\<required\></sup>, city <sup>\<required\></sup>, state
  <sup>\<required\></sup>, zip <sup>\<required\></sup>, country
  <sup>\<required\></sup>
- Phone number
- Email address <sup>\<required\></sup>
- Password <sup>\<required\></sup>
  - Password strength must be indicated as well. Weak passwords will be
    rejected. A weak password is ≤10 characters. A strong password is above 17
    characters.
- At least three security questions <sup>\<required\></sup>
  - The user inputs custom security questions and answers
- The answer to a simple CAPTCHA challenge of some type <sup>\<required\></sup>
  - Example: `what is 2+2=?`
  - You must not call out to any API for this, your team must make the CAPTCHA

When admins create new accounts, they must provide at minimum **a first, middle,
and last name, an email address, and a _secure_ password** along with anything
your team deems necessary. The rest they can leave blank if they choose. When an
admin-made customer account is logged into for the first time, the user must be
prompted to change their password and fill in the rest of the required fields,
if they admin didn't already fill them in, before they can interact with the
rest of the app.

> Note: admin accounts do not need all of this information (like address, phone
> number, etc). Also, only the root account can modify the information of an
> admin that isn't themselves. Admins cannot modify each other's information,
> but they can view it. Only customers can delete their own accounts.

## Requirement 8

**Guests can authenticate (login) using at minimum their first name, last name,
and password.**

A guest can login to your app using _at least_ their first name, last name, and
the correct password. You are free to require any other information you want for
logins.

> Warning: if a new user signs up giving the same credentials (first name, last
> name, and password) as an already existing user, the two users must be able to
> coexist in your app **without bad security implications** and **without making
> the user remember anything extra other than their name and password and other
> information required during registration**. Be careful.

Additional constraints:

- Users will be prevented from logging in for 1 hour after 3 failed login
  attempts
  - The user will always see how many attempts they have left
- There will be optional fully functional "remember me" functionality so that
  users do not have to constantly login
- If users log in and want to be "remembered," they will not be logged out until
  they manually log out
- If users log in but do not want to be "remembered," they will be automatically
  logged out after 15 minutes of no activity
- After successfully logging in, the user will be redirected to their private
  dashboard

## Requirement 9

**Your app must cross-check flyer information against the No Fly List when
booking a ticket.**

Ensure that no flight can be booked for a passenger whose information matches in
the No Fly List, which can be queried using the API.

If the name, birthdate, and sex match a passenger's info, they should be denied.
This should happen even if the passenger's information is in a different case
(i.e. they spell their name in ALL CAPS).

To test the No Fly List, you can use the following passenger information:

- **name** (first middle last): Restricted User Flier
- **birthdate** (day, month, year): 25, 12, 1985
- **sex**: male

This passenger will always exist in the No Fly List and should be denied the
ability to book flights when encountered.

> Keep in mind the passenger flying is different than the customer account. A
> customer can buy a ticket for a different person.

## Requirement 10

**If a customer does not remember their password, they can use their security
questions to recover their account.**

If a customer has forgotten their login credentials, they will be able to
recover their account using their security questions. Admins and others
**cannot** use security questions to recover their account.

## Requirement 11

**All views displaying flight/ticket information will ensure that information
updates asynchronously.**

Whenever a flight's information in the API (or your database) changes, the
flight information on your pages must _eventually_ update to match the new
information **without the page refreshing** or the user doing anything extra
(like pressing a refresh button). This type of automatic updating is
"asynchronous" (or "ajaxian"), since they occur outside of the usual control
flow of the web page.

For example, if a customer is viewing their ticket and their flight’s gate
changes in the API, the new gate number will replace the old gate number on the
customer’s ticket view. This way, when the user looks down at their phone after
a moment, they'll see the latest gate number and not get confused. They’ll be
happy. We want happy customers.

On the other hand, when data updates are synchronous, the customer would have to
do something like refresh the page to see the updated ticket view with the new
gate number. But how does a customer know when to refresh the view? Will they
keep pressing F5 over and over? What if they miss their flight because they
forgot to refresh and went to the wrong gate? They’ll be angry. We don’t want
angry customers.

You are free to use any technique you can imagine to implement asynchronous data
updates.

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
injection attacks. User-generated outputs will not be vulnerable to XSS or
similar attacks.**

As for database security, any passwords present in the database must be
[hashed](https://auth0.com/blog/hashing-passwords-one-way-road-to-security)
(**not** encrypted). We recommend using a
[salted SHA-256 hash construction](https://auth0.com/blog/adding-salt-to-hashing-a-better-way-to-store-passwords)
or something similar. You don't need to do anything fancy.
[There](https://web.archive.org/web/20230127140833/https://nakedsecurity.sophos.com/2013/11/20/serious-security-how-to-store-your-users-passwords-safely/)
[are](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)
[many](https://www.vaadata.com/blog/how-to-securely-store-passwords-in-database)
[tutorials](https://dev.to/kmistele/how-to-securely-hash-and-store-passwords-in-your-next-application-4e2f)
for how to safely store passwords and other credentials in a database.

> Passwords stored in your database in
> [cleartext](https://simple.wikipedia.org/wiki/Cleartext), hashed incorrectly,
> or re-encoded (e.g. with
> [base64](https://base64.guru/blog/base64-encryption-is-a-lie)) will earn your
> team zero points for this requirement.

## Requirement 14

**The app will
[fail gracefully](https://getbootstrap.com/docs/5.0/forms/validation/#server-side)
when exceptional conditions are encountered.**

This includes handling API errors during fetch, login errors, random exceptions,
[showing spinners](https://getbootstrap.com/docs/4.4/components/spinners) when
content needs to load, etc.

> Every so often,
> [the API will respond with an `HTTP 555` error](https://hsccdfbb7244.docs.apiary.io/#/introduction/rules-of-api-access)
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
