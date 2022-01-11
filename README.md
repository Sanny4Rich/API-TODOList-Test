# API-TODOList-Test

Start from "$ docker-compose up -d --build server"

Default url 127.0.0.1:8080

Urls: 
/list/{user}/{order-field}/{order}
* user - user key 
* order-field - default "id", possible (doneAt, createdAt, priority)
* order - ASC or DESC (ASC - default)
* filters in query parameters, available ('status', 'priority', 'title', 'description')

Only with user key show all tasks like tree
_________________________________________________________________________________________________

/create/{user}
method POST
create a new task

*string status TODO or DONE,
*int priority,
*string title,
*null|string description,
*string userKey,
*int parentId,

return string with message
_______________________________________________________________________________________

/get/{user}/{id}

get task by ID and user

______________________________________________________________________________
/update/{userKey}/{id}
method POST

all fields like in create but ID is needed to update task
__________________________________________________________________________________

/delete/{userKey}/{id}
method POST

Delete task and all children if no one of children is not have status DONE
