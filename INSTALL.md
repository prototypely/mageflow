# Quick start in 5 steps #

1. Get MageFlow extension with version number 2.0.0 or higher.
1. Install MageFlow extension to at least 2 Magento instances
1. Create Oauth consumers in Magento instances
1. Connect source Magento instance to target Magento instance with MageFlow
1. Read Ground Rules, start exchanging change items

# Detailed instructions for getting started #

## Step 1 – download MageFlow 2.0 release ##

## Step 2 – install extension to your source and target Magento instances ##

Flush or turn off Magento caches before installation.

## Step 3 – Create Oauth Consumer in target Magento ##

Step-by-step guide follows:

## Login to Magento admin ##
1. Go to System / Web Services / REST – Roles
1. Click “Add admin role”
1. Give new role name “Administrators” and enter your admin password
1. Under “Role API resources” set Resource Access to “All”
1. Save role
1. Go to System / Web Services / REST – Attributes
1. Select user type Admin
1. Set resource access to “All”
1. Save
1. Go to System / Permissions / Users
1. Select your user
1. Under “REST Role” check radio button by “Administrators”
1. Save your user
1. Go to System / Web Services / REST – Oauth Consumers
1. Take note of Oauth consumer. It’s needed in step 2.
1. Click “Add new”
1. Give it a name “MageFlow”
1. Click “Save”

Your target Magento instance is ready to be used with MageFlow now!

## Step 4 – connect source Magento to target Magento ##

## In source Magento ##

## go to MageFlow / Connect ##

1. Enter target Magento’s base URL to the field “Target base URL”. NB! Be sure to add the trailing /
1. Copy-paste Oauth consumer keys from target Magento Oauth consumer (noted in step 1)
1. Click “Save Config”
1. Click “Connect to target Magento”
1. You will be redirected to target Magento’s Oauth dialog. Click “Authorize” to give Mageflow access to resources of target Magento.
1. Take a look at MageFlow extension configuration in source Magento and enable / disable all data types you want to have Change Items created for.

Your source Magento instance is now ready for sending some Change Items to the target Magento instance!

# Step 5 – Migrate your first Change Item by following the Ground Rules #

Here are a few Ground Rules when using MageFlow that should be followed in order to avoid headache caused by failed migrations or inconsistent data.

* Always be sure you have a recent backup of your Magento database before pushing data from MageFlow to that instance or pulling data from MageFlow to that instance.
* Always push the dependencies first to make sure your instances are up to date:
* Migrate websites / store groups / store views (short: W/SG/SV)  before anything else to make sure you have W/SG/SV with correct MF GUID’s in all of your instances
* Migrate root category before migrating subcategories.
* Migrate attributes before migrating attribute sets or products.
* Keep migrations atomic (push or pull items one by one) whenever possible
* For example, create a new CMS page or save an existing one in source Magento. Now navigate to MageFlow / Send and find your Change Item under “CMS Pages”. Select “Push” from the Action dropdown on your item. A dialog for Change Item description appears. Give it a meaningful description like “Changed formatting of titles on page 1” and click OK. If the status of Change Item changed to “sent” then it’s sent to your target Magento.

Now it’s time to go to your target Magento and see if the Change Item is there. Go to target Magento admin and navigate to MageFlow / Inbox. Click “Apply” on your incoming Change Item to accept it and it will be applied to your target Magento.