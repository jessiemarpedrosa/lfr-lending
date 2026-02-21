# WP Theme - Small Web App for Micro Lending

This is a small web app for Micro Lending that consist of Customers, Loans, Transactions and other details relevant to them.

This uses wpDataTable plugin to display the data and use the filter functionalities of the plugin.

The theme is a child theme of Bootspress WP Theme - https://pcm.wordpress.org/themes/bootspress/

Custom coded features/pages are:

## Transactions
- add-multiple-transactions.php - This will add/edit multiple payments of specific customers of each loan


## Loans
- loan-details-list.php - Display all the records and view each payments per Loans, can search with Customer No. or Loan No.


## Reports
- daily-transactions.php - Reports for daily transactions and have the ability to print in PDF


## Demand Letters
- demand-letter-1.php - Generate and print the 1st Demand Letter for a specific loan
- demand-letter-2.php - Generate and print the 2nd Demand Letter for a specific loan
- Both letters calculate outstanding balance including overdue penalties
- Print events are tracked with a timestamp saved to the `lfr_loans` table (`tracked_dl1`, `tracked_dl2`)
- A warning is shown if a demand letter has already been printed, including the date it was issued


## Other Info
- all scripts are found in js/myscript.js