# Coding Jobs Api
> A simple rest api for my coding jobs bot. [endpoint](https://developer.coding-jobs.oyaa.co.ke)

## API Endpoints
- <code>GET</code> [https://developer.coding-jobs.oyaa.co.ke/all-jobs](https://developer.coding-jobs.oyaa.co.ke/all-jobs) ➡️ all jobs (new and old)
    > parameters
    - `limit=2` The number of jobs to return (default is 10)

- <code>GET</code> [https://developer.coding-jobs.oyaa.co.ke/new](https://developer.coding-jobs.oyaa.co.ke/new) ➡️ new jobs
    > parameters
    - `limit=3` The number of jobs to return (default is 10) optional
    - `platform=twitter` new jobs for a certain platform (e.g. twitter, telegram) optional

- <code>POST</code> [https://developer.coding-jobs.oyaa.co.ke/new](https://developer.coding-jobs.oyaa.co.ke/new) ➡️ post a new job
    > Auth required: Yes (OAuth 2.0)
    ```php
    //headers
    array(
        "Authorization: Bearer [ACCESS_TOKEN_HERE]",
        "Content-Type: application/json"
      );
    ```

    > Data
    ```JSON
    {
        "title":"Data Analyst",
        "location":"Nairobi",
        "company":"Potential Staffing Limited",
        "summary":"A small job description",
        "salary":"KSh 40000",
        "link":"https://www.brightermonday.co.ke/listings/data-analyst-mpjwzz",
        "post_date":"5d ago"
    }
    ```
  ## Licence
