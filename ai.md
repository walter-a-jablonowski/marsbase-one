
I am thinking about making a web project that helps Elon to build his Mars Colony. Elon already is buillding a big rocket, which is the most important thing. But to be able to build a second (sustainable) civilation on Mars the mars colonists need much more stuff.

I'd like to make a full features web app that is requirement based. In a hierarchical navigation users can suggest requirements for items required for building the base on mars. Then users can add different types of items (e.g. goods, community projects, etc.) that fulfill the requirement.

- use typical data fields for each item type, but use ony the most important for now for simplicity
- also include availability date
- requirement also have data fields e.g. a list of related items (e.g. power → habitat)

For the requirements as well as the items users can Up- or downvode (users may change) which is used for sorting. Also add basic filter and search functions.

Community projects can ask for funding, which we implement as a dummy for now. We will add a payment feature later.

Users can be persons or organizations. Persons may be part of an Organisation. Users have a personal profile page and they can:

- Follow/favorite requirements and items

Make the web app full features including login system. Make it based on bootstrap 5.3 but alter the design using styles so that it matches "mars colony" theme of the app. Make the UI responsive and work on all devices.

We use PHP with simple classes and put all data in the /data folder (no database):

- ids: derive it from the field "name":
  - convert each word to first character uppercase
  - then remove all non alpha numeric chars
  - add a short random string to the end

```
/data
  /users

    id:                   email is used as id for users
    type:                 "person" or "organization"
    password:             Hashed password
    name:                 Full name or organization name
    memberIds:            if person: array of org ids, if org: array member ids
    image:                uploaded file (gets some hash as file name on upload)
    bio:                  Short biography or description
    expertise:            Areas of expertise/skills
    location:             Current location (Earth/Mars)
    itemsFollowing:       Array of item IDs the user follows
    reqFollowing:         Array of requirement IDs the user follows
    itemScore:            Array of items up- or downvoted like [{ID: 1|-1}, ...]
    reqScore:             Array of requirements up- or downvoted
    modifiedAt:           YYYY-MM-DD HH:MM:SS

  /requirements (hierarchical)

    id                    derive from name
    childIds:             IDs of child requirements (for hierarchy)
    name
    primaryImage:         uploaded file (gets some hash as name on upload)
    images:               Array of image hashes
    status:               "proposed", "validated"
    description:          Short description
    detailed:             Longer explanation
    relatedIDs:           Array of related requirement IDs
    itemIDs:              Array of item IDs that fulfill this requirement
    score:                Calculated from up/downvotes
    createdBy:            User ID of creator
    modifiedAt:           YYYY-MM-DD HH:MM:SS

  /items

    id                   derive from name
    requirementIds:      Array of requirement IDs that this item fulfills
    name
    description:         Short description
    projectLead:         User ID of project lead
    availabilityDate:    Expected availability (percentage or specific date)
    primaryImage:        uploaded file (gets some hash as name on upload)
    images:              Array of image hashes

    score:               Calculated from up/downvotes

    mass:                Mass in kg
    volume:              Volume in cubic meters
    shape

    fundingGoal:         Amount of funding needed
    contributions:       Array of user who contribute like [{user: USER_ID, time: TIME, amount: AMOUNT}, ...]
    currentFunding:      Calculated from contributions
    volunteerRoles:      Types of volunteers needed

    createdBy:           User ID of creator
    modifiedAt:          YYYY-MM-DD HH:MM:SS
```
