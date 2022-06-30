import React from "react";
import slugify from "slugify";

import images from './data';

const imageAssetDir = '../portraits';

const Gallery = () => (
  <div className="center">
    <div className="gallery flex-container">
      {images.map(({ altText, fileName, title }) => {
        const key = `pic-${slugify(title)}`;
        const src = `${imageAssetDir}/${fileName}`;
        return (
          <div className="flex-item portrait-box">
            <div
              className="flex-item portrait"
              key={key}
              style={{'background-image': `url(${src})`}}
            >
              <img alt={altText} src="../images/frame.png" />
            </div>
            <figcaption>{title}</figcaption>
          </div>
        );
      })}
    </div>
  </div>
);

export default Gallery;
