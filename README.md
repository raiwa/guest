# Guest Checkout

## Installation
1. Copy/upload all new files from the upload directory into your store directory:
2. [Required] In Admin =>Modules => Content[login] install and configure the module 'Login without Account Form'.
3. [Required] In Admin =>Modules => Customer Data install and configure the module 'Guest'.
4. [Required] In Admin =>Modules => Customer Data for all Modules you wish to show on the create_account_pwa page, tick the page 'checkout_guest' in the 'Pages' list.  
Only the E-Mail module is required for PWA to work. However you need to add the page also to the required modules for your shipping and payment modules and checkout process to work properly.
5. [Optional] If you wish to use the "Offer set password to guest" option and offer to opt for a regular account:  
 In Admin =>Modules => Content[checkout_success] install and configure the module 'PWA Keep Account.
6. [Required] If you are using the Date of Birth module, you need to activate the datepicker on the create_account pwa page, go to Admin -> Modules -> Header Tags, choose the entry Datepicker JQuery and click Edit. In the file list choose checkout_guest.php and click Save.
7. [Optional] If you wish to use the "Review Link" options in the order confirmation mail and/or staus update mail:  
In Admin => Configuration => E-Mail Options => Use MIME HTML When Sending Emails => Set to "True"
8. [Optional] In Admin => Orders => Use "Add links to produkt reviews" to add them to
the comments.
Works for guest orders and regular orders and will be shown in the comment area if added.
9. [Optional] settings for stores selling virtual-downloadable products:  
In Admin =>Modules => Content[login] 'Login without Account Form'.  
    * Allow guest checkout for virtual products =>  
        * False: for any order containing virtual products, guest checkout will be disabled  
        * True: for any order containing virtual products, guest checkout will be enabled  
    * Guests Exclude Download Payment Modules  
Allows to select payment modules like cod, check/money order, bank transfer etc. which are not to be instantly disabled for any virtual product order for guests.  
Needs the previous option "Allow guest checkout for virtual products" to be set to "True"  
NOTE: If you add a new payment module, uninstall the 'Login without Account Form' module and reinstall to update the payment module listing.
10. [Optional] settings to add review links to guest and regular order confirmation e-mails:  
In Admin =>Modules => Content[login] 'Login without Account Form'.  
    * Add Review Links to Guest Order Mail  
True  
    * Add Review Links to Standard Order Mail  
True

---

Ready! You can now use PWA!
NOTE: The PWA login form is visible for customers only if their shopping cart is not empty and they are not logged into their regular account!

### Instructions

NOTE: In a normal checkout process, guest accounts are automatic deleted in the checkout success page.
It may happen that a guest doesn't return to this page for example coming back from an external payment
service or if he does not complete the order after he already filled in his guest data.
Only in these cases a guest account may stay registered in the database and you can keep your database
clean deleting old guest accounts from time to time.
