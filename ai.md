
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
    profileImage
    bio:                  Short biography or description
    expertise:            Areas of expertise/skills
    location:             Current location (Earth/Mars)
    following:            Array of requirement/item IDs the user follows
    upvoted:              Array of item/requirement IDs upvoted
    downvoted:            Array of item/requirement IDs downvoted
    modifiedAt:           YYYY-MM-DD HH:MM:SS

  /requirements (hierarchical)

    id                    derive from name
    childIds:             IDs of child requirements (for hierarchy)
    name
    images:               Array of image URLs
    status:               "proposed", "validated"
    description:          Short description
    detailed:             Longer explanation
    relatedIDs:           Array of related requirement IDs
    itemIDs:              Array of item IDs that fulfill this requirement
    userRating:           Calculated from up/downvotes
    createdBy:            User ID of creator
    modifiedAt:           YYYY-MM-DD HH:MM:SS

  /items

    id                   derive from name
    requirementIds:      Array of requirement IDs that this item fulfills
    name
    description:         Short description
    projectLead:         User ID of project lead
    availabilityDate:    Expected availability (percentage or specific date)
    primaryImage
    images:              Array of image URLs

    userRating:          Calculated from up/downvotes

    mass:                Mass in kg
    volume:              Volume in cubic meters
    shape
    powerRequirement:    Power needed in watts
    maintenance:         maintenance requirements (time, duration)
    lifespan:            Expected lifespan

    fundingGoal:         Amount of funding needed
    currentFunding:      Current amount raised
    contributors:        Array of user IDs who contribute
    volunteerRoles:      Types of volunteers needed

    createdBy:           User ID of creator
    modifiedAt:          YYYY-MM-DD HH:MM:SS
```

fields
nav
