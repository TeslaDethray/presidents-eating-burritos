import React from "react";
import { Helmet } from "react-helmet";

import Gallery from "./gallery";

const SITE_NAME = "Presidents Eating Burritos";

const Home = () => (
  <div className="application">
    <Helmet>
      <meta charSet="utf-8" />
      <title>{SITE_NAME}</title>
      <link rel="canonical" href="https://www.tesladethray.me/peb" />
      <link href="../favicon/favicon.ico" rel="icon" />
      <link href="../styles/styles.css" rel="stylesheet" type="text/css" />
      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin />
      <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@1&display=swap" rel="stylesheet" />
    </Helmet>
    <h1 className="center">{SITE_NAME}</h1>
    <Gallery />
    <center>Images generated with <a href="https://www.craiyon.com/">CrAIyon</a></center>
  </div>
);

export default Home;