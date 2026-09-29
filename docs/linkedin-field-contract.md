# LinkedIn field contract

The careers form sends this field with the existing `multipart/form-data` request:

| Property | Value |
| --- | --- |
| Field name | `linkedin_url` |
| Type | String / URL |
| Required | No |
| Maximum length | 255 characters |
| Accepted scheme | `https` |
| Accepted host | `linkedin.com` or any subdomain such as `www.linkedin.com` |
| Empty value | Empty string or `null` |

Example:

```text
linkedin_url=https://www.linkedin.com/in/username
```

Backend validation must reject malformed URLs, non-HTTPS URLs, and URLs outside the LinkedIn domain. Store the value in the nullable database column `job_applications.linkedin_url`. The migration is available at `database/migrations/2026-09-28-add-linkedin-url.sql`.
