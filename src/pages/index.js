import React from "react"
import { Helmet } from "react-helmet"

import Gallery from "./gallery/Gallery"

const SITE_NAME = "Presidents Eating Burritos"

const Home = () => (
  <div className="application">
    <Helmet>
      <meta charSet="utf-8" />
      <title>{SITE_NAME}</title>
      <link rel="canonical" href="https://www.tesladethray.me/presidents-eating-burritos" />
      <link href="/favicon/favicon.ico" rel="icon" />
      <link href="/styles/styles.css" rel="stylesheet" type="text/css" />
    </Helmet>
    <Gallery />
  </div>
)

export default Home
