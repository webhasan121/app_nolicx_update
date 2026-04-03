import { Head } from "@inertiajs/react";

export default function SiteTitle({ title, appName = "nolicx" }) {
  const finalTitle = title ? `${title} | ${appName}` : appName;
  return <Head title={finalTitle} />;
}

