
I am thinking about making a web project that helps Elon to build his Mars Colony. Elon already is buillding a big rocket, which is the most important thing. But to be able to build a second (sustainable) civilation on Mars the mars colonists need much more stuff.

I'd like to make a full features web app that is requirement based. In a hierarchical navigation users can suggest requirements for items required for building the base on mars. Then users can add solutions ("items") that fulfill the requirement.

Pages:

- Start
- Login
- Register
- Requirements
  - left side: quick nav = hierarchical navigation of requirements for quick access
  - right side, content area: list of requirement items, with:
    - filter and sorting controls
    - each list item:
      - show most important requirement information (including image)
      - enables navigate to list of sub requirements
      - also enables to go to the requirement page of where all information is visible as well as the items for this requirement are listed
- People
  - list of people showing their expertise for mars
- we use no seperate items page
- Profile
- Settings (currently empty)
- Logout

For the requirements as well as the items users can Up- or downvode (users may change) which is used for sorting. Also add basic filter and search functions.

Community projects can ask for funding, which we implement as a dummy for now. We will add a payment feature later.

Users can be persons or organizations. Persons may be part of an Organisation. Users have a personal profile page and they can:

- Follow/favorite requirements, items and users

Make the web app full features including login system and basic error display in case of app errors. Make it based on bootstrap 5.3 but alter the design using styles so that it matches "mars colony" theme of the app. Make the UI responsive and work on all devices.

We use config.yml, simple PHP code with simple classes and put all data in the /data folder (no database).

We use speacking File names for all files in data folders, e.g. for users:

- derive it from the field "name"
- convert each word to first character uppercase
- then remove all non alpha numeric chars
- add a short random string to the end

Data files:

```
/data
  /users

    id:                   numeric (use a json file sequence.json that has the last used ids)
    type:                 "person" or "organization"
    email:                email (unique)
    password:             Hashed password
    name:                 Full name or organization name
    bio:                  Short biography or description
    expertise:            Areas of expertise/skills
    image:                uploaded file jpg, jpeg, png (gets some hash as file name on upload)
    location:             

    memberIds:            if person: array of org ids, if org: array member ids
    followedItems:        Array of item IDs
    followedReq:          Array of requirement IDs
    followedUsers:        Array of user IDs
    itemScore:            Array of items up- or downvoted like [{id: ID, score: 1|-1}, ...]
    reqScore:             Array of requirements up- or downvoted

    modifiedAt:           YYYY-MM-DD HH:MM:SS

  /requirements (hierarchical)

    id                    numeric
    childIds:             IDs of child requirements (for hierarchy), requirements can have multiple parents (e.g. power supply needed for different things)
    relatedIDs:           Array of related requirement IDs (e.g. dependencies)
    userIDs:              Array of user IDs that may edit this requirement
    name
    status:               "proposed", "validated"
    description:          Short description
    detailed:             Longer explanation
    primaryImage:         uploaded file (gets some hash as name on upload)
    images:               Array of image hashes

    itemIDs:              Array of item IDs that fulfill this requirement

    score:                Calculated from up/downvotes

    createdBy:            User ID of creator
    modifiedAt:           YYYY-MM-DD HH:MM:SS

  /items (only the creator may edit an item)

    id                   numeric
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
    contributions:       Array of user who contribute like [{user: USER_ID, time: YYYY-MM-DD HH:MM:SS, amount: AMOUNT}, ...]
    currentFunding:      Calculated from contributions
    volunteerRoles:      Types of volunteers needed

    createdBy:           User ID of creator
    modifiedAt:          YYYY-MM-DD HH:MM:SS
```
