SELECT id, category, url, `datetime` 
FROM fusion5vn4q_documents
WHERE url IN ( SELECT url
    FROM fusion5vn4q_documents
    GROUP BY url
    HAVING COUNT(*) > 1)
ORDER BY url, id;
