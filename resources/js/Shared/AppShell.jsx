import React from 'react';
import { Link, usePage } from '@inertiajs/inertia-react';

const menuItems = [
    { label: 'Dashboard', href: '/' },
    { label: 'Clientes', href: '/clientes' },
    { label: 'Propostas', href: '/propostas' },
    { label: 'Aprovações', href: '/aprovacoes' },
    { label: 'Instalações', href: '/instalacoes' },
];

export default function AppShell({ children }) {
    const { auth } = usePage().props;

    return (
        <div className="inertia-shell">
            <header className="inertia-header">
                <div className="inertia-container">
                    <div className="inertia-header-top">
                        <h1 className="inertia-brand">Agente Alluz • Inertia</h1>
                        <div className="inertia-user">{auth?.user?.name}</div>
                    </div>
                    <nav className="inertia-nav">
                        {menuItems.map((item) => (
                            <Link key={item.href} href={item.href} className="inertia-nav-link">
                                {item.label}
                            </Link>
                        ))}
                    </nav>
                </div>
            </header>
            <main className="inertia-container inertia-main">{children}</main>
        </div>
    );
}
