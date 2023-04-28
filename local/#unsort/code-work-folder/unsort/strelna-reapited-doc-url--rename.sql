CREATE TEMPORARY TABLE `tmp_doc` 
SELECT z.*
FROM
(SELECT id, category, url, `datetime` 
FROM fusion5vn4q_documents
WHERE url IN ( SELECT url
    FROM fusion5vn4q_documents
    GROUP BY url
    HAVING COUNT(*) > 1)) z;


UPDATE fusion5vn4q_documents as a, tmp_doc as b SET a.url= CONCAT(a.url,a.id) WHERE a.id = b.id;

-- SELECT * FROM tmp_doc;

DROP TABLE tmp_doc;

