import * as React from "react";
const SvgAoAngola = (props) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width={16}
    height={12}
    fill="none"
    {...props}
  >
    <mask
      id="AO_-_Angola_svg__a"
      width={16}
      height={12}
      x={0}
      y={0}
      maskUnits="userSpaceOnUse"
      style={{
        maskType: "luminance",
      }}
    >
      <path fill="#fff" d="M0 0h16v12H0z" />
    </mask>
    <g mask="url(#AO_-_Angola_svg__a)">
      <path
        fill="#1D1D1D"
        fillRule="evenodd"
        d="M0 0v12h16V0H0Z"
        clipRule="evenodd"
      />
      <mask
        id="AO_-_Angola_svg__b"
        width={16}
        height={12}
        x={0}
        y={0}
        maskUnits="userSpaceOnUse"
        style={{
          maskType: "luminance",
        }}
      >
        <path
          fill="#fff"
          fillRule="evenodd"
          d="M0 0v12h16V0H0Z"
          clipRule="evenodd"
        />
      </mask>
      <g fillRule="evenodd" clipRule="evenodd" mask="url(#AO_-_Angola_svg__b)">
        <path fill="#F50100" d="M0 0v6h16V0H0Z" />
        <g filter="url(#AO_-_Angola_svg__c)">
          <path
            fill="#FCFF01"
            d="M7.775 6.696c.388-.479.509-1.098.509-1.485 0-2.059-2.569-3.198-2.569-3.198 1.766 0 3.733 1.432 3.733 3.198 0 .748-.256 1.442-.686 1.994.787.392 1.453.699 1.453.699.247.162.316.594.154.841a.536.536 0 0 1-.741.154s-.632-.373-1.017-.626a15.58 15.58 0 0 0-.598-.37c-.505.34-1.111.54-1.763.54 0 0-2.348-.288-2.281-1.492 0 0 .568.435 2.241.435.266 0 .5-.037.705-.103-.724-.395-1.338-.709-1.338-.709-.248-.162-.89-.983-.729-1.23.162-.248 1.143.032 1.39.194 0 0 .343.472.829.76.214.128.456.263.708.398ZM6.59 4.901l-.505.337.162-.577-.371-.368.502-.02.212-.57.213.57h.5l-.37.388.186.543L6.59 4.9Z"
          />
          <path
            fill="#FFEA42"
            d="M7.775 6.696c.388-.479.509-1.098.509-1.485 0-2.059-2.569-3.198-2.569-3.198 1.766 0 3.733 1.432 3.733 3.198 0 .748-.256 1.442-.686 1.994.787.392 1.453.699 1.453.699.247.162.316.594.154.841a.536.536 0 0 1-.741.154s-.632-.373-1.017-.626a15.58 15.58 0 0 0-.598-.37c-.505.34-1.111.54-1.763.54 0 0-2.348-.288-2.281-1.492 0 0 .568.435 2.241.435.266 0 .5-.037.705-.103-.724-.395-1.338-.709-1.338-.709-.248-.162-.89-.983-.729-1.23.162-.248 1.143.032 1.39.194 0 0 .343.472.829.76.214.128.456.263.708.398ZM6.59 4.901l-.505.337.162-.577-.371-.368.502-.02.212-.57.213.57h.5l-.37.388.186.543L6.59 4.9Z"
          />
        </g>
      </g>
    </g>
    <defs>
      <filter
        id="AO_-_Angola_svg__c"
        width={6.489}
        height={6.974}
        x={3.968}
        y={2.013}
        colorInterpolationFilters="sRGB"
        filterUnits="userSpaceOnUse"
      >
        <feFlood floodOpacity={0} result="BackgroundImageFix" />
        <feColorMatrix
          in="SourceAlpha"
          result="hardAlpha"
          values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
        />
        <feOffset />
        <feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.2 0" />
        <feBlend
          in2="BackgroundImageFix"
          result="effect1_dropShadow_270_54949"
        />
        <feBlend
          in="SourceGraphic"
          in2="effect1_dropShadow_270_54949"
          result="shape"
        />
      </filter>
    </defs>
  </svg>
);
export default SvgAoAngola;
