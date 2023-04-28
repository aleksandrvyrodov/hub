((w, d) => {
  let propTable = d.getElementById("ib_prop_list"),
    propLines = propTable.querySelectorAll("tr[id]"),
    propListObj = [],
    propRawCSV = "ID;NAME;TYPE;ACTIVE;MULTIPLE;REQUIRE;SORT;CODE\r\n";

  propLines.forEach((propLine) => {
    if (!propLine.querySelector("td:first-child").innerText) return;

    let propObj = {
			id: propLine.querySelector('td:first-child').innerText,
      name: propLine.querySelector('input[name$="NAME"]').value,
      type: propLine.querySelector('select[name$="TYPE"]').selectedOptions[0]
        .innerText,
      active: propLine.querySelector('input[id$="ACTIVE_Y"]').checked,
      multiple: propLine.querySelector('input[id$="MULTIPLE_Y"]').checked,
      require: propLine.querySelector('input[id$="REQUIRED_Y"]').checked,
      order: propLine.querySelector('input[name$="SORT"]').value,
      code: propLine.querySelector('input[name$="CODE"]').value,
    };
    
    propListObj.push(propObj);
  });

  propListObj.forEach((propObj) => {
    propRawCSV += `${propObj.id};${propObj.name};${propObj.type};${
      propObj.active ? "Y" : "N"
    };${propObj.multiple ? "Y" : "N"};${propObj.require ? "Y" : "N"};${
      propObj.order
    };${propObj.code};\r\n`;
  });

  function CSVFile(csv) {
    let link = document.createElement("a");

    link.id = "lnkDwnldLnk";

    let blob = new Blob(["\ufeff", csv], {
        type: "text/csv;charset=utf-8;",
      }),
      csvUrl = w.URL.createObjectURL(blob),
      filename = "propExport" + ".csv";

    link.setAttribute("download", filename);
    link.setAttribute("href", csvUrl);
    link.click();

    w.URL.revokeObjectURL(link.href);
  }

  CSVFile(propRawCSV);
})(window, document);
