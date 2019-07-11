[UAT Bugherd to Teamwork Bridge](https://some.hostedwebsite.com/) | [Documentation]

# Usage

This tool provides a bridge between Teamwork and Bugherd. Use 3.5 steps in this interface to create the necessary pieces in syncing Bugherd Bugs to Teamwork automagically per project. 

Steps below reflect the steps used in the index.php file of this repository

1. Create a Teamwork Task List for UAT
2. Create a Bugherd Project List for UAT
3. Select your Bugherd Project List to create the Webhook that auto sends bugs.
4. Send your client the UAT document and / or give the generated Javascript code to your developer

ᕕ( ᐛ )ᕗ 

That's it.

## Troubleshooting

This tool acts as a way to create new lists in both Teamwork and Bugherd.
If one in either place exists for your client, it will be ignored. 
Before createing a new UAT task list, it would be best to delete the previous one to avoid confusion, as this is MVP.

A small API log is here for each request: `/api/webhook_log.txt`. 

For anything else, find Daniel
