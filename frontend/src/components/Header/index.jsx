"use client"; // This directive makes the component a Client Component

import { usePathname } from 'next/navigation';

const Header = () => {
  const pathname = usePathname();

  let title;
  switch (pathname) {
    case '/works':
      title = "WORK WE'RE PROUD";
      break;
    case '/aboutus':
      title = "ABOUT US";
      break;
    case '/blog':
      title = "BLOG";
      break;
    default:
      title = "WELCOME"; // Default title or you can leave it empty
  }

  return (
    <div className="relative h-screen">
      <img
        src="/images/bg.png"
        alt="Work Image"
        className="absolute inset-0 object-cover w-full h-full"
      />
      <div className="relative flex items-center justify-center h-full w-full p-0 m-0">
  <div className="text-center text-white w-full">
    <svg viewBox="0 0 1300 100" className="mx-auto w-full">
      <text
        x="50%"
        y="50%"
        textAnchor="middle"
        alignmentBaseline="middle"
        className="text-6xl lg:text-8xl font-bold"
        fill="none"
        stroke="#FFFFFF"
        strokeWidth="2"
        dominantBaseline="middle"
        style={{ letterSpacing: '2px' }}
      >
        {title}
      </text>
    </svg>
    <p className="mt-8 text-xs lg:text-lg">
      We professionally deliver digital solutions using agile <br />
      methodology to help established companies and startups perform by
      providing high-quality <br /> software development services.
    </p>
  </div>
</div>

    </div>
  );
};

export default Header;
