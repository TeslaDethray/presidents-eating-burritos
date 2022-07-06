import React from 'react'
import slugify from 'slugify'

import images from '../data'
import {Helmet} from 'react-helmet';

const imageAssetDir = 'images';
const portraitAssetDir = 'portraits';

const Gallery = () => (
  <div className='gallery'>
    <Helmet>
      <link href='/styles/gallery.css' rel='stylesheet' type='text/css' />
    </Helmet>
    <img alt='Museum facade top left' src={`/${imageAssetDir}/museum_a1.png`} />
    <img alt='Museum facade top center' src={`/${imageAssetDir}/museum_a2.png`} />
    <img alt='Museum facade top right' src={`/${imageAssetDir}/museum_a3.png`} />
    <img alt='Museum facade center left' src={`/${imageAssetDir}/museum_b1.png`} />
    <div className='gallery-container'>
      <div className='slides'>
        {images.map(({ altText, fileName, title }) => {
          const key = `pic-${slugify(title)}`;
          return (
            <div><img
              alt={altText}
              key={key}
              src={`/${portraitAssetDir}/${fileName}`}
            /></div>
          );
        })}
      </div>
    </div>
    <img alt='Museum facade center right' src={`/${imageAssetDir}/museum_b3.png`} />
    <img alt='Museum facade bottom left' src={`/${imageAssetDir}/museum_c1.png`} />
    <img alt='Museum facade bottom center' src={`/${imageAssetDir}/museum_c2.png`} />
    <img alt='Museum facade bottom right' src={`/${imageAssetDir}/museum_c3.png`} />
  </div>
);

export default Gallery;
