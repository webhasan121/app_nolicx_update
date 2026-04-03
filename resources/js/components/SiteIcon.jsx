import { Head } from "@inertiajs/react";

export default function SiteIcon({ href = "/logo.png" }) {
  return <Head><link rel="shortcut icon" href={href} /></Head>;
}

