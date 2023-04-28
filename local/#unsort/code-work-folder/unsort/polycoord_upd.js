let poly = [
  [0, 0],
  [0, 1],
  [1, 1]
];

function inPoly(poly, p) {
  const poly_n = poly.length;
  let in_poly = false;

  for (let i = 0, j = poly_n - 1; i < poly_n; j = i++)
    in_poly = (((poly[i][1] <= p[1]) && (p[1] < poly[j][1])) || ((poly[j][1] <= p[1]) && (p[1] < poly[i][1]))) &&
      (p[0] > (poly[j][0] - poly[i][0]) * (p[1] - poly[i][1]) / (poly[j][1] - poly[i][1]) + poly[i][0]) ? !in_poly : in_poly

  return in_poly;
}

inPoly(poly, [0, 0]);